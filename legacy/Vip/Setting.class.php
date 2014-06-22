<?php
/** installation settings
 * name / value pairs
 * @package Vip
 */
class Vip_Setting extends Pasta_TableRow
{
    /** Override
     * @return Vip_Setting
     */
    public static function getById($id)
    {
        global $CUSTOMER;
        
        $o = parent::getObjectById(__CLASS__, $id);
        if ($o && $o->customerId == $CUSTOMER->id) {
            return $o;
        } else {
            return null;
        }
    }
    
    /** get a settings obj.
     * @param string
     * @param Vip_Product
     * @param Vip_Customer
     * @return Vip_Setting
     */
    public static function getByNameAndProductAndCustomer($name, Vip_Product $product, Vip_Customer $customer)
    {
        $o = new self;
        $o->setName($name);
        $o->setCustomerId($customer->getId());
        $o->setProductId($product->getId());
        $ok = $o->load($onlyKeys = false);
        if (!$ok) { // look for default value
            $o->setCustomerId(0);
            $ok = $o->load($onlyKeys = false);
        }
        return $ok ? $o : null;
    }

    /** get a setting obj. for current customer
     * @param string
     * @param string
     * @return Vip_Setting
     */
    public static function getByNameAndProductName($name, $productName)
    {
        $product = $GLOBALS['CUSTOMER']->getProductByName($productName);
        if (!$product) {
            trigger_error('Invalid product: ' . $productName);
            return null;
        }
        return self::getByNameAndProductAndCustomer($name, $product, $GLOBALS['CUSTOMER']);
    }

    /** 
     * Get a named setting obj.
     * @param string
     * @param Vip_Customer
     * @return Vip_Setting
     */
    public static function getByNameAndCustomer($name, $customer)
    {
        $settingQuery = new self;
        $settingQuery->customerId = $customer->id;
        $settingQuery->name = $name;
        $setting = $settingQuery->findOne();
        if (!$setting) {
            // look for default value at customerId 0
            $settingQuery->customerId = 0;
            $setting = $settingQuery->findOne();
        }
        return $setting;
    }

    /** get a setting obj. for current customer and product
     * @param string
     * @return string
     */
    public static function getValueByName($name)
    {
        global $CUSTOMER, $PRODUCT;

        if (isset($CUSTOMER, $PRODUCT)) {
            if ($setting = self::getByNameAndProductAndCustomer($name, $PRODUCT, $CUSTOMER)) {
                return $setting->getValue();
            }
        }
        return '';
    }
    
    /** get a setting obj. for current customer and product
     * @param string
     * @return string
     */
    public static function getAllByProduct(Vip_Product $product)
    {
        global $CUSTOMER;

        $setting = new self;
        if (isset($CUSTOMER)) {
            $setting->customerId = $CUSTOMER->id;
        } else {
            return array();
        }
        $setting->productId = $product->id;

        return $setting->findAll();
    }
}

?>
