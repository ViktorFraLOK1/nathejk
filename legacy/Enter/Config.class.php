<?php
/** installation settings
 * name / value pairs
 * @package Enter
 */
abstract class Enter_Config 
{
    /** get a settings value
     */
    static function getByName($name)
    {
        static $products = array();
        static $settings = array();

        global $CUSTOMER;
        $cid = $CUSTOMER->id;

        // cache product
        if (!isset($products[$cid])) {
            $products[$cid] = $CUSTOMER->getProductByName("enter");
        }
        $product = $products[$cid];
        if (!$product) {
            trigger_error("Enter product not enabled for customer '" . $CUSTOMER->name . "'");
        }

        // cache setting
        if (!isset($settings[$cid])) {
            $settings[$cid] = array();
        }
        if (!isset($settings[$cid][$name])) {
            $settings[$cid][$name] = Vip_Setting::getByNameAndProductAndCustomer($name, $product, $CUSTOMER);
        }
        return $settings[$cid][$name];
    }

    static function getValueByName($name)
    {
        $conf = self::getByName($name);
        return $conf ? $conf->value : false;
    }
}

?>
