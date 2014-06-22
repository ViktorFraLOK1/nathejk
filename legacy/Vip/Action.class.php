<?php

/**
 * @package Vip
 */
class Vip_Action extends Pasta_TableRow
{
    /**
     * @param  int
     * @return Vip_User  the action with the specified id, or null if no such
     *                   action exists.
     */
    static function getById($id)
    {
        return parent::getObjectById(__CLASS__, $id);
    }

    /**
     * @param  name
     * @return Vip_User  the action with the specified name, or null if no
     *                   such action exists.
     */
    static function getByName($name)
    {
        $action = new Vip_Action();
        $action->setName($name);
        return $action->findOne();
    }

    static function getByIdOrName($actionIdOrName) {
        if (is_numeric($actionIdOrName)) {
            $action = Vip_Action::getById($actionIdOrName);
        } else {
            $action = Vip_Action::getByName($actionIdOrName);
        }
        return $action;
    }

    /**
     * @return  array  array of Vip_Action objects
     */
    static function getAll()
    {
        return parent::getAllObjects(__CLASS__, 'productId');
    }

    /**
     * @return  array  array of Vip_Action objects
     */
    static function getAllByProductId($productId)
    {
        global $CUSTOMER;
        $action = new Vip_Action();
        $action->productId = $productId;
        return $action->findAll();
    }

    /**
     * @return  int
     */
    function getProductId()
    {
        return $this->getColumn('productId');
    }
    /**
     * @param  int
     */
    function setProductId($value)
    {
        $this->setColumn('productId', $value);
    }

    /**
     * @return  Vip_Product
     */
    public function getProduct()
    {
        return Vip_Product::getById((int) $this->__get('productId'));
    }

    /**
     * @return  int
     */
    function getId()
    {
        return $this->getColumn('id');
    }
    /**
     * @param  int
     */
    function setId($value)
    {
        $this->setColumn('id', $value);
    }

    /**
     * @return  string
     */
    function getName()
    {
        return $this->getColumn('name');
    }
    /**
     * @param  string
     */
    function setName($value)
    {
        $this->setColumn('name', $value);
    }

    function getDescription()
    {
        return $this->getColumn('description');
    }
    function setDescription($value)
    {
        $this->setColumn('description', $value);
    }
    
    function getParameterListMethod()
    {
        return $this->getColumn('parameterListMethod');
    }
    function setParameterListMethod($value)
    {
        $this->setColumn('parameterListMethod', $value);
    }
    
    function getParameterValueMethod()
    {
        return $this->getColumn('parameterValueMethod');
    }
    function setParameterValueMethod($value)
    {
        $this->setColumn('parameterValueMethod', $value);
    }
    
    function getParameterLabelMethod()
    {
        return $this->getColumn('parameterLabelMethod');
    }
    function setParameterLabelMethod($value)
    {
        $this->setColumn('parameterLabelMethod', $value);
    }
    
    function getParameterValueNamePairs() 
    {
        $list = $this->getParameterListMethod();
        if(!$list) { return array(); }
        
        list($className, $method) = explode('::', $list);
        $pairs = call_user_func(array($className, $method));
        if(!is_array($pairs)) { return array(); }
        
        $value = $this->getParameterValueMethod();
        $name = $this->getParameterLabelMethod();
        if(!$value || !$name) { return $pairs; }

        $arr = array();
        foreach($pairs as $pair) {
            $arr[$pair->$value()] = $pair->$name();
        }
        return $arr;
    }

    function getParameterObjects()
    {
        $list = $this->getParameterListMethod();
        list($className, $methodName) = explode('::', $list);
        if ($className) {	 
            return call_user_func(array($className, $methodName));	 
        } else {	 
            return array();	 
        }	 
    }

    function getParameterValues()
    {
        return array_keys($this->getParameterValueNamePairs());
    }
}

?>
