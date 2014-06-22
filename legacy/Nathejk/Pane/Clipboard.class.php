<?php

/**
 * @package Sbs
 */
class Sbs_Pane_Clipboard extends Sbs_Pane
{
    /**
     * @see Sbs_Pane::getType()
     */
    public function getType()
    {
        return 'clipboard';
    }
    
    /**
     * @see Sbs_Pane::getTitle()
     */
    public function getTitle()
    {
        return 'Clipboard';
    }
    
    /**
     * @see Sbs_Pane::initialize()
     */
    public function initialize()
    {
        // Nothing to do here
    }
    
    /**
     * The form results should be displayed if submit has been 
     * sent.
     * 
     * @return bool
     */
    public function getDisplayItems()
    {
        // The clipboard always displays the contents
        return true; 
    }
    
    /**
     * @return array
     */
    public function getItems()
    {
        return Sbs_Clipboard::getAll();
    }
}

?>