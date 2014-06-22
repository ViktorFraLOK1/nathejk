<?php

/**
 * @package Sbs
 */
class Nathejk_Namespace
{
    /**
     * Form name in the namespace
     * @var string
     */
    protected $name;
    
    /**
     * The form mode, either POST or GET
     * @var string
     */
    protected $mode = 'POST';
    
    /**
     * @var array
     */
    protected $_validModes = array('POST', 'GET');
    
    /**
     * Overides for namespace values
     * @var array
     */
    protected $overide = array();
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $name
     */
    public function __construct($name = 'default')
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    
    /**
     * Sets the form mode, either POST or Get
     */
    public function setMode($mode) 
    {
        if (!in_array($mode, $this->_validModes)) {
            return false;
        }
        $this->mode = $mode;
        return true;
    }
    
    /**
     * @param string $name
     * @param string $value
     */
    public function setValueOveride($name, $value) 
    {
        $this->overide[$name] = $value;
    }
    
    /**
     * Returns the for data depending on the mode
     * 
     * This is either $_POST or $_GET
     * 
     * @return array
     */
    protected function getDataContainer()
    {
        switch ($this->mode) {
            case 'GET':
                return $_GET;
                break;
            case 'POST':
                return $_POST;
                break;
            default:
                return array();
        }
    }
    
    /**
     * Get the namespace.
     * 
     * The namespace is constructed from the const namespace 
     * definition and the name.
     * 
     * @return string
     */
    public function getNamespaceString($elementName = null)
    {
        if ($elementName) {
            return  $this->name . '[' . $elementName . ']';
        }
        
        return $this->name;
    }
    
    /**
     * @return bool
     */
    public function getHasValues()
    {
        $dataContainer = $this->getDataContainer();
        
        return isset($dataContainer[$this->name]);
    }
    
    
    /**
     * @param string $name
     */
    public function getValue($name)
    {
        if (isset($this->overide[$name])) {
            return $this->overide[$name];
        }
        
        if (!$this->getHasValues()) {
            return null;
        }
        
        $dataContainer = $this->getDataContainer();
        
        if (!isset($dataContainer[$this->name][$name])) {
            return null;
        }
        
        return $dataContainer[$this->name][$name];
    }
    
    /**
     * @return array
     */
    public function getValues()
    {
        if (!$this->getHasValues()) {
            return array();
        }
        
        $dataContainer = $this->getDataContainer();
        
        return $dataContainer[$this->name];
    }
}

?>
