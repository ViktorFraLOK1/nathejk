<?php
/**
 * @package Enter
 */
class Enter_Product extends Vip_Product
{

    /**
     * Override from Vip_Product
     * @return array
     */
    public function getTableDependencies()
    {
        $deps = array(
            'groupTable'    => array('keyColumnName' => 'customerId', 
                'dependencies' => array(
                'groupUser' => array('keyColumnName' => 'groupId'),
                'groupUserHistory' => array('keyColumnName' => 'groupId'),
                )), 
            );
        foreach (Enter_User::getUserObject()->getTables() as $userTable) {
            $deps[$userTable] = array('keyColumnName' => 'customerId');
        }
        return $deps;
    }
}
?>
