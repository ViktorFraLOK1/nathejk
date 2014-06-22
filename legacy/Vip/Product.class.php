<?php

/**
 * @package Vip
 */
class Vip_Product extends Pasta_TableRow
{
    private $_customer = null;

    /**
     * Lookup id by name
     * @param string
     * @return int
     */
    public static function getIdByName($name)
    {
        $p = self::getByName($name);
        return $p ? $p->id : 0;
    }

    function getCustomer()
    {
        return $this->_customer;
    }

    function setCustomer(Vip_Customer $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * @param int
     * @return Vip_Product|null  the Vip_Product with the specified id, or null
     *                           if no such product exists
     */
    public static function getById($id)
    {
        return parent::getObjectById(__CLASS__, $id);
    }

    /**
     * @return array  array of Vip_Product instances
     */
    public static function getAll()
    {
        return parent::getAllObjects(__CLASS__, 'sortOrder, title');
    }

    /**
     * @param string
     * @return Vip_Product|null  the Vip_Product with the specified name, or
     *                           null if no such product exists
     */
    public static function getByName($name)
    {
        $o = new Vip_Product();
        $o->setName($name);
        return $o->findOne();
    }

    /**
     * @param   string
     * @return  Vip_Product
     */
    public static function getByHostname($hostname)
    {
        $info = self::parseHostname($hostname);
        if (!isset($info['product'])) {
            return null;
        }
        $product = self::getByName($info['product']);
        if (!$product) {
            trigger_error('Product not found: ' . $info['product']);
            return null;
        }
        if (isset($info['customer'])) {
            $product->setCustomer(Vip_Customer::getByName($info['customer']));
        }
        return $product;
    }

    /**
     * Parses a rule-based hostname. Returns an associative array with one or
     * more of the following keys:
     * - backend: bool, true if this is a backend site
     * - product: a product name
     * - customer: a customer name
     * - prefix: a prefix whose meaning is determined by the individual product
     * @param  string
     * @return array
     */
    static function parseHostname($hostname)
    {
        $info = array();
        // strip fixed part and an optional port and/or trailing period
        $s = preg_replace('/\.(peytz|form)\.dk\.?(:[0-9]+)?$/','', $hostname, 1, $count);
        // handle only peytz.dk hosts
        if ($count == 0) {
            return $info;
        }
        $parts = array_reverse(explode('.', $s));
        if ($parts[0] == 'f1' || $parts[0] == 'f2') {
            array_shift($parts);
        }
        if (preg_match('/^dev[0-9]*/', $parts[0]) || $parts[0] == 'cgidev') {
            // zap 'dev'/'cgidev'
            array_shift($parts);
            // zap user name after checking for vip
            $info['backend'] = strpos($parts[0], 'vip-') === 0;
            array_shift($parts);
        } elseif ($parts[0] == 'vip' || $parts[0] == 'vip2') {
            $info['backend'] = true;
            array_shift($parts);
        } else {
            $info['backend'] = false;
        }
        // product
        if (isset($parts[0]) && $parts[0]) {
            $info['product'] = $parts[0];
            array_shift($parts);
        }
        // customer
        if (isset($parts[0]) && $parts[0]) {
            $info['customer'] = $parts[0];
            array_shift($parts);
        }
        // the rest
        if (isset($parts[0]) && $parts[0]) {
            $info['prefix'] = join('.', array_reverse($parts));
        }
        return $info;
    }

    /** get / set 
     */
    function getId()
    {
        return $this->getColumn('id');
    }
    function setId($value)
    {
        return $this->setColumn('id', $value);
    }
    function getName()
    {
        return $this->getColumn('name');
    }
    function setName($value)
    {
        return $this->setColumn('name', $value);
    }
    function getTitle()
    {
        $languageCode = $this->customer ? $this->customer->languageCode : 'name';
        $translation = Pasta_Translation::getByPackageNameAndLanguageCode($this->name, $languageCode);
        if ($translation) {
            $title = $translation->getStringByName('vipProductTitle');
        }
        return empty($title) ? $this->getColumn('title') : $title;
    }
    function setTitle($value)
    {
        return $this->setColumn('title', $value);
    }

    /**
     * @param   string  an optional prefix whose meaning is determined by the
     *                  individual product, e.g. a Kollage site id
     * @return  string  a URL with a trailing slash
     */
    public function getFrontendUrl($prefix = null)
    {
        $site = new Vip_Site();
        $site->productId = $this->id;
        $customer = $this->getCustomer();
        if ($customer) {
            $site->customerId = $customer->id;
        }
        $site->setOrderBySql('isPrimary DESC');
        // Site may or may not exist in the database
        $found = $site->load(false);
        if ($found) {
            $site = $site->getMutation();
        }
        return $site->url;
    }

    /**
     * @return  string  a URL with a trailing slash
     */
    public function getStaticUrl()
    {
        $customer = $this->getCustomer();
        
        if (isset($_SERVER['PEYTZ_DEV'])) {
            return "//static." . $this->getName() . "." .
                $_SERVER['PEYTZ_DEV_USER'] . '.' . $_SERVER['PEYTZ_HOSTNAME'] . '.peytz.dk/';
        } else {
            return "//static.peytz.dk/" . $this->getName() . "/";
        }
    }

    /**
     * @return  string  a URL with a trailing slash
     */
    public function getBackendUrl()
    {
        $customer = $this->getCustomer();
        if (!$customer) {
            trigger_error('Customer not set');
            return false;
        }
        return 'https://'  . $customer->facility->backendHostname .
            '/' . $customer->name . '/' . $this->name . '/';
    }

    /** 
     * Returns the uri for the frontend metadesign files for this customer/product combo
     *
     * @return  string  url to where metadesign files can be found, i.e. "//borsen-tips.metadesign.test.dev.peytz.dk/"
     */
    public function getMetadesignUrl()
    {
        global $CUSTOMER;
        
        if (!$CUSTOMER) {
            return '';
        }
        
        $dev        = isset($_SERVER['PEYTZ_DEV']);
        $devUser    = !empty($_SERVER['PEYTZ_DEV_USER']) ? $_SERVER['PEYTZ_DEV_USER'] : '';
        
        $mdUserFilePath = '/home/' . $devUser . '/www/metadesign';
        if ($devUser && !file_exists($mdUserFilePath)) {
            $devUser = 'test';
        }
        
        if (!$dev || $devUser == 'virtual') {
        	return $this->getStaticUrl() . 'metadesign/';
        } else {
            $metadesignName = Vip_Setting::getByNameAndProductAndCustomer('metadesignName', $this, $CUSTOMER);
            if (!$metadesignName) {
                trigger_error('This CUSTOMER/PRODUCT combination (' . $CUSTOMER->title . ',' . $this->title . ') is missing the \'metadesignName\' setting in Vip.', E_USER_NOTICE);
                return '';
            } else {
                // use metadesign in the URI with dashes when on test
                // but with dots when on a developers private area
        	    return '//' . $metadesignName->value . ($devUser == 'test' ? '-metadesign-' : '.metadesign.') . $devUser . '.' . $_SERVER['PEYTZ_HOSTNAME'] . '.peytz.dk/';
        	}
        }
    }
    
    /**
     * Returns the filesystem path to this product, i.e. to the directory¨
     * containing "backend/", "frontend/" etc.
     * @return  string  a filesystem path including a trailing "/".
     * @see  getPath()
     */
    public function getPath()
    {
        return self::getPathByName($this->getName());
    }
    
    /**
     * Returns the filesystem path to this product frontend template directory
     * @return  string  a filesystem path including a trailing "/".
     * @see  getPath()
     */
    public function getFrontendTemplatePath()
    {
        return $this->getPath() . 'templates/';
    }

    /**
     * Returns the filesystem path to this product backend template directory
     * @return  string  a filesystem path including a trailing "/".
     * @see  getPath()
     */
    public function getBackendTemplatePath()
    {
        return $this->getPath() . 'templates/';
    }

    /**
     * A way to get the product path without creating a product instance.
     * @return  string  a filesystem path including a trailing "/".
     * @see  getPath()
     */
    public static function getPathByName($name)
    {
        $dir = dirname($_SERVER['DOCUMENT_ROOT']) . '/../' . $name;
        if (!is_dir($dir) && isset($_SERVER['PEYTZ_PRODUCTS_FALLBACK_DIR'])) {
            $dir = $_SERVER['PEYTZ_PRODUCTS_FALLBACK_DIR'] . $name;
        }
        if (!is_dir($dir)) {
            trigger_error('Cannot find product path');
        }
        return realpath($dir) . '/';
    }

    /**
     * Converts a CamelCased product name used in a class name prefix to a
     * hyphen-separated lowercase name, e.g. from "DrDerude" to "dr-derude".
     * @param   string
     * @return  string
     * @see  getPath()
     */
    public static function getNameByClassName($className)
    {
        list($prefix) = explode('_', $className);
        return strtolower(preg_replace('/([a-z0-9])([A-Z])/', '\1-\2', $prefix));
    }

    /** get a settings value
     * @param string
     * @return string
     */
    function getSettingByName($name)
    {
        $setting = Vip_Setting::getByNameAndProductAndCustomer($name, $this, $this->_customer);
        return $setting ? $setting->value : false;
    }

    /** get a settings value
     * @param string
     * @return string
     */
    function getSettings()
    {
        return Vip_Setting::getAllByProductAndCustomer($this, $this->_customer);
    }

    /**
     * Returns the users with access to this product.
     * @return array  array of Vip_User instances
     */
    public function getUsers()
    {
        $productUsers = array();
        foreach ($this->getCustomer()->getVipUsers() as $user) {
            if ($user->hasPermission('use_product', $this->getId())) {
                $productUsers[] = $user;
            }
        }
        return $productUsers;
    }
    
    /**
     * Enable use of specific product classes.
     * The className column in vip.product overrides the default (Vip_Product).
     * @param array
     * @return string
     */
    protected function getClassNameByRow($row)
    {
        return isset($row['className']) && $row['className'] 
            ? $row['className']
            : parent::getClassNameByRow($row);
    }

    /**
     * Return recursive array of foreign key dependencies "top down".
     * A depency is an assoc. array with the table name as key and a value of an array of a column name and possibly a depedency.
     * Top level parent is customer -> customerId column.
     * @return array
     */
    public function getTableDependencies()
    {
        trigger_error('Override this to enable moving product data to another facility');
    }
}

?>
