<?php
/**
 * @package Sbs
 */
class Nathejk_Pane
{

    protected static $nextPaneId = 1;

    /**
     * Namespace of the pane
     *
     * @var Sbs_Namespace
     */
    protected $namespace;

    /**
     * Id of the pane, used in the check boxes in
     * display results
     *
     * @var int
     */
    protected $paneId;


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->namespace = new Nathejk_Namespace($name);

        // Store the next pane id and increment the count
        $this->paneId = self::$nextPaneId;
        self::$nextPaneId++;

        if ($this->namespace->getValue('columnView')) {
            setcookie($name . '_columnViewName', $this->namespace->getValue('columnView'));
        }
        else if (isset($_COOKIE[$name . '_columnViewName'])) {
            $this->namespace->setValueOveride('columnView', $_COOKIE[$name . '_columnViewName']);
        }
        // Setup default column view
        if ($this->namespace->getValue('columnView') === null) {
            $columnView = Nathejk_DisplayColumns::getDefaultViewName();
            $this->namespace->setValueOveride('columnView', $columnView);
        }
    }

    /**
     * Used by some of the pane classes
     * for intialization
     */
    public function initialize()
    {
        return;
    }

    /**
     * Used in templates to known what extra
     * option elements to display in the form
     * @return string
     */
    public function getType()
    {
        return 'pane';
    }

    /**
     * Pane title, used in the header of the result
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Pane';
    }

    /**
     * Id of the pane, used in the check boxes in
     * display results
     *
     * @return int
     */
    public function getPaneId()
    {
        return $this->paneId;
    }

    /**
     * Get the namespace of the pane
     *
     * @return Sbs_Namespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get the names of the columns that the pane is currently
     * displaying.
     *
     * @return array
     */
    public function getColumnNames()
    {
        $columnView = $this->namespace->getValue('columnView');

        $columnNames = Nathejk_DisplayColumns::getColumnNamesByViewName($columnView);
        if ($columnNames === false) {
            return Nathejk_DisplayColumns::getBasicColumns();
        }

        return Nathejk_DisplayColumns::getColumnNamesByViewName($columnView);
    }

    /**
     * Should the results be displayed.
     * Relevant for search results.
     *
     * TODO: rename to something that makes sense
     *
     * @return bool
     */
    public function getDisplayItems()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return array();
    }
}

?>
