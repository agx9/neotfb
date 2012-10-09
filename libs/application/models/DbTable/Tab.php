<?php

class Application_Model_DbTable_Tab extends Zend_Db_Table_Abstract
{

    protected $_name = 'tabs';

    /**
    * Class instance
    * @var Application_Db_Table_Post
    */
    private static $_instance;

    


    public function saveUser($fbUserId)
    {
        $select = $this->select();
        $tabs = $this->createRow();
        $tabs->userId = $fbUserId;        
        $tabs->save();
        
    }

    public function saveTab($pageData)
    {
        $select = $this->select();
                
        $select->where('pageId = ?', $pageData['pageId']);
        $tab = $this->fetchRow($select);
        
        
         //if it does not then create it
        if(!$tab) {
             $tab = $this->createRow();
        }


        
        $tab->pageId = $pageData['pageId'];
        $tab->tabHtml = $pageData['tab'];

        $tab->save();

        
    }




    public function getTab($pageData)
    {
        
        $select = $this->select();
        $select->where('pageId = ?', $pageData['page']['id']);
        return $this->fetchRow($select);


    }


    public function createTab($pageData)
    {
        


        
    }
        

            

    /**
    * Fuction to get class instance
    * @return Application_Db_Table_Config
    */
    public static function getInstance()
    {
         //return new self();
         if( !isset(self::$_instance) ){
             $instance = new self();
             self::$_instance = $instance;
         }
         return self::$_instance;
    }

}