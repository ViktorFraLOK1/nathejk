<?php

/**
 * @package Sbs
 */
class Nathejk_Pane_CheckIn extends Nathejk_Pane
{
    /**
     * @see Sbs_Pane::getType()
     */
    public function getType()
    {
        return 'checkIn';
    }
    
    public function getTitle()
    {
        return 'Fangster';
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
        // The inbox always displays the contents
        return true; 
    }
    
    /**
     * @return array
     */
    public function getItems()
    {
        $query = new Nathejk_CheckIn;
        $query->deletedUts = 0;
        return $query->findAll();
    }
}

?>
