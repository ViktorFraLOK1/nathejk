<?php

/**
 * @package Sbs
 */
class Sbs_Pane_Search extends Sbs_Pane
{
    /**
     * @var Sbs_Solr_SearchIndex
     */
    protected static $searchIndex = null;
    
    /**
     * @var Sbs_Search_Form
     */
    protected $form;
    
    /**
     * @var string
     */
    protected $searchTitle;
    
    /**
     * @see Sbs_Pane::getType()
     */
    public function getType()
    {
        return 'search';
    }
    
    /**
     * @see Sbs_Pane::getTitle()
     */
    public function getTitle()
    {
        if ($this->searchTitle) {
            return $this->searchTitle;
        }
        
        return 'Search';
    }
    
    /**
     * @see Sbs_Pane::initialize()
     */
    public function initialize()
    {
        $namespace = $this->getNamespace();
        $index = self::getSearchIndex();
        
        // Construct a new form using the namespace of the pane and the
        // fields from the solr index
        $this->form = new Sbs_Search_Form($namespace, $index->getFields());
        
        // If the namespace contains submitted values we initialize the form with them
        if ($namespace->getHasValues()) {
            $this->form->setStateFromArray($namespace->getValues());
        }
        
        // The form has a special select box that allows modifying its behavior
        if ($namespace->getValue('searchOption') !== null) {
            
            $searchOption = strtolower($namespace->getValue('searchOption'));
            
            if ($searchOption == 'new') {
                $this->form->clearState();
                $namespace->setValueOveride('submit', false);
            } elseif ($searchOption == 'edit') {
                $namespace->setValueOveride('submit', false);
            } elseif (is_numeric($searchOption)) {
                $savedSearchTable = new Sbs_Search_Saved();
                $savedSearchTable->id = $searchOption;
                $savedSearch = $savedSearchTable->findOne();
                
                if ($savedSearch) {
                    $this->form->setStateFromArray($savedSearch->getSearchState());
                    $namespace->setValueOveride('submit', true);
                    $this->searchTitle = $savedSearch->title;
                }
            }
        }
    }
    
    /**
     * @return Sbs_Solr_SearchIndex
     */
    protected static function getSearchIndex()
    {
        if (self::$searchIndex === null) {
            self::$searchIndex = new Sbs_Solr_SearchIndex();
        }
        
        return self::$searchIndex;
    }
    
    /**
     * @return Sbs_Search_Form
     */
    public function getForm()
    {
        return $this->form;
    }
    
    /**
     * The form results should be displayed if submit has been 
     * sent.
     * 
     * @return bool
     */
    public function getDisplayItems()
    {
        return (bool) $this->namespace->getValue('submit');
    }
    
    /**
     * @return array
     */
    public function getItems()
    {
        $index = self::getSearchIndex();
        
        $query     = $this->form->getSolrQuery();
        $documents = $index->getDocumentsByQuery($query);
        $type      = $this->form->getElementValue('type');
        $items     = $index->getSearchResultItemListByDocuments($documents, array($type));
        
        return $items;
    }
    
    
}

?>