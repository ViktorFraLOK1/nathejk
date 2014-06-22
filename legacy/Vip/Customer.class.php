<?php

/**
 * @package Vip
 */
class Vip_Customer extends Vip_Doer
{
    /**
     * @return  string
     */
    function getDefaultTable()
    {
        // There is no common doer table
        return 'vip_customer';
    }

    /**
     * @return  bool
     */
    public function isValid()
    {
        if (preg_match('@[^a-z0-9]@', $this->getName())) {
            $this->setErrorString('Invalid name');
            return false;
        }
        if (!$this->title) {
            $this->setErrorString('Long name not specified');
            return false;
        }
        if (!$this->managerVipUserId) {
            $this->setErrorString('Customer must have a manager specified');
            return false;
        }
        if (!$this->consultantVipUserId) {
            $this->setErrorString('Customer must have a consultant specified');
            return false;
        }

        return parent::isValid();
    }

    /**
     * @param string hostname
     * @return Vip_Customer|null
     */
    public static function getByHostname($hostname)
    {
        $site = Vip_Site::getByHostname($hostname);
        $customer = $site ? $site->customer : null;
        if ($customer && $customer->isActive) {
            return $customer;
        }
        return null;
    }

    /**
     * Get the products used by this customer. This is defined as any product
     * that either
     * 1) is accessible by at least one user belonging to this customer, or
     * 2) is used in a Vip_Site for this customer.
     * @return  array  array of Vip_Product objects
     * @see Vip_User::getPermittedProducts()
     */
    function getProducts()
    {
        $db = $this->getPearDb();

        // The query is big, but it does use indexes. Anyway, the involved
        // tables almost never change, so we utilize MySQL's query cache
        $sql = "(
            # User permissions
            SELECT DISTINCT pe_u.parameterValue AS productId
            FROM user u
            JOIN permission pe_u
              ON pe_u.doerId = u.id
              AND pe_u.allowed = '1'
              AND pe_u.actionId = 6   # 6 = use_product
            WHERE u.customerId = " . $this->id . "

            ) UNION (

            # Customer permissions
            SELECT DISTINCT pe_c.parameterValue AS productId
            FROM permission pe_c
            WHERE pe_c.doerId = " . $this->id . "
              AND pe_c.allowed = '1'
              AND pe_c.actionId = 6   # 6 = use_product

            ) UNION (

            SELECT productId
            FROM site s
            WHERE s.customerId = " . $this->id . "

            ) UNION (

            # Kollage is always on
            SELECT 1

            )";
        $productIds = $db->getCol($sql);
        $productIds = array_filter(array_unique($productIds), 'intval');

        $product = new Vip_Product;
        $product->columnIn('id', $productIds);
        // Customer-specific products are only enabled for that customer.
        $product->addWhereSql('customerId IN (0, ' . $this->id . ')');
        $product->setOrderBySql('sortOrder,title');
        $products = $product->findAll();
        foreach ($products as $product) {
            $product->setCustomer($this);
        }

        return $products;
    }

    /** Get a product
     * @param string product name
     */
    function getProductByName($name)
    {
        static $products = array();

        if (!isset($products[$this->id])) {
            $products[$this->id] = array();
        }
        if (!array_key_exists($name, $products[$this->id])) {
            if ($product = Vip_Product::getByName($name)) {
                $product->setCustomer($this);
            }
            $products[$this->id][$name] = $product;
        }
        return $products[$this->id][$name];
    }

    /**
     * All collections owned by this customer
     * @return  array           array of Vip_Collection
     */
    public function getCollections()
    {
        $obj = new Vip_Collection;
        $obj->setCustomerId($this->getId());
        return $obj->findAll();
    }

    /**
     * collection by id  owned by this customer
     * @return  Vip_Collection
     */
    public function getCollectionById($id)
    {
        $obj = new Vip_Collection;
        $obj->setId($id);
        $obj->setCustomerId($this->getId());
        return $obj->findOne();
    }

    /**
     * @return  Vip_Collection
     */
    public function newCollection()
    {
        $obj = new Vip_Collection;
        $obj->setCustomerId($this->getId());
        return $obj;
    }

    /**
     * All editions owned by this customer
     * @return  array           array of Vip_Edition
     */
    public function getEditions()
    {
        $obj = new Vip_Edition;
        $obj->setCustomerId($this->getId());
        return $obj->findAll();
    }

    /**
     * edition by id  owned by this customer
     * @return  Vip_Edition
     */
    public function getEditionById($id)
    {
        $obj = new Vip_Edition;
        $obj->setId($id);
        $obj->setCustomerId($this->getId());
        return $obj->findOne();
    }

    /**
     * default editions owned by this customer
     * @return  array           array of Vip_Edition
     */
    public function getDefaultEditions()
    {
        $obj = new Vip_Edition;
        $obj->setCustomerId($this->getId());
        $obj->setIsDefault(1);
        return $obj->findAll();
    }

    /**
     * @param   string  an IP address in xxx.xxx.xxx.xxx notation
     * @return  bool
     */
    public function hasAccessFromIp($ip)
    {
        // The only check for ipPeytz wrt Vip and Login is done here
        if ($ip == $_SERVER['REMOTE_ADDR'] && isset($_SERVER['ipPeytz'])) {
            return true;
        }
        return Vip_Access::hasAccess($this->id, $ip);
    }

    function getPermissionParentId()
    {
        return 0;
    }

    /**
     * @return Vip_Facility
     */
    public function getFacility()
    {
        return Vip_Facility::getById($this->facilityId);
    }

    /**
     * @return  string  a URL with a trailing slash
     */
    function getVipWebroot()
    {
        return 'https://' . $this->facility->backendHostname . '/' .
            $this->getName() . '/vip/';
    }

    /**
     * Returns an array containing the users belonging to this customer.
     * @return  array  array of Vip_User objects
     * @deprecated Use getVipusers instead
     */
    public function getUsers($active = true)
    {
        if (!empty($_SERVER['PEYTZ_DEV'])) {
            trigger_error('deprecated, use getVipUsers');
        }
        return $this->getVipUsers($active);
    }
    /**
     * Returns an array containing the users belonging to this customer.
     * @return  array  array of Vip_User objects
     */
    public function getVipUsers($active = true)
    {
        $query = new Vip_User();
        $query->setCustomerId($this->getId());
        $query->setIsActive(intval($active));
        return $query->findAll();
    }

    /**
     * @param   string
     * @return  Vip_User
     */
    public function getVipUserByUsername($username)
    {
        $user = new Vip_User();
        $user->setCustomerId($this->getId());
        $user->setUsername($username);
        return $user->findOne();
    }

    /**
     * @param   string
     * @return  Vip_User
     * @deprecated Use getVipUserByUsername instead
     */
    public function getUserByUsername($username)
    {
        if (!empty($_SERVER['PEYTZ_DEV'])) {
            trigger_error('deprecated, use getVipUserByUsername');
        }
        return $this->getVipUserByUsername($username);
    }

    /**
     * @param   int
     * @return  Vip_User
     */
    public function getVipUserById($userId)
    {
        $user = new Vip_User();
        $user->customerId = $this->getId();
        $user->id = $userId;
        return $user->findOne();
    }

    /**
     * Enter user
     * @return Enter_User|null
     */
    public function getUserById($userId)
    {
        $classname = Enter_User::getUserClassName();
        $userQuery = new $classname;
        $userQuery->customerId = $this->id;
        $userQuery->id = $userId;
        return $userQuery->findOne();
    }

    /**
     * @return  array  array of URI strings
     */
    function getPasswordCheckerUris()
    {
        $urisString = $this->getColumn('passwordCheckerUris');
        $uris = $urisString ? explode(',', $urisString) : array();
        return $uris;
    }

    /**
     * @return Vip_Customer  the customer with the specified id, or null if
     *                        no such customer exists.
     */
    static function getById($id)
    {
        return parent::getObjectById(__CLASS__, $id);
    }

    /**
     * Returns the customer with the specified name, or null if no such
     * customer exists.
     * @return Vip_Customer|null
     */
    public static function getByName($name)
    {
        $customer = new Vip_Customer();
        $customer->setName($name);
        return $customer->findOne();
    }

    /**
     * @return  array  array of Vip_Customer instances
     */
    public static function getAll()
    {
        return parent::getAllObjects(__CLASS__, 'title');
    }

    /**
     * @return  array  array of Vip_Customer instances
     */
    public static function getAllActive()
    {
        $customer = new Vip_Customer();
        $customer->isActive = 1;
        return $customer->findAll();
    }

    /**
     * @return  array  array of Vip_Customer instances
     */
    public static function getAllByFacilityId($facilityId)
    {
        $customer = new Vip_Customer();
        $customer->facilityId = $facilityId;
        return $customer->findAll();
    }

    /**
     * Get a setting value
     * @param string
     * @param string
     * @return string|null
     */
    public function getSettingValueBySettingName($settingName)
    {
        $setting = Vip_Setting::getByNameAndCustomer($settingName, $this);
        if (!$setting) {
            return null;
        }
        return $setting->value;
    }


    /**
     * Private/internal smtp host
     * @return Pasta_SmtpIp|null
     */
    public function getSmtpIp()
    {
        return Pasta_SmtpIp::getByPrivateIpAddress(gethostbyname($this->getSettingValueBySettingName('smtpHost')));
    }

}

?>
