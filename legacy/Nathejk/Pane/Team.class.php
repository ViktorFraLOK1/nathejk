<?php

/**
 * @package Sbs
 */
class Nathejk_Pane_Team extends Nathejk_Pane
{
    /**
     * @see Sbs_Pane::getType()
     */
    public function getType()
    {
        return 'team';
    }
    
    public function getTitle()
    {
        return 'Team';
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
        $query = new Nathejk_Klan;
        $query->deletedUts = 0;
        switch ($this->namespace->getName()) {
            case 'spejder' : 
                $query->typeName = 'patrulje';
                $query->addWhereSql('title != ""');
                break;
            case 'lok' : 
                $query->typeName = 'lok';
                break;
            case 'post' : 
                $query->typeName = 'post';
                break;
            case 'senior' :
                $query->columnIn('typeName', array('klan', 'super'));
                break;
            default:
                $query->typeName = '';
                

        }
        return $query->findAll();
    }
}

?>
