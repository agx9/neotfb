<?php

class Application_Model_DbTable_Config extends Zend_Db_Table_Abstract
{

    protected $_name = 'config';

    /**
    * Class instance
    * @var Application_Db_Table_Config
    */
    private static $_instance;



    public function getConfig()
    {
        $select = $this->select();
        //Zend_Debug::dump($this->fetchAll($select));exit;

        //get all config values
        $configs = $this->fetchAll($select);

        //create an object of std class for config
        $configObj = new stdClass();


        //iterating through all config entries
        foreach ($configs as $config) {
            //get config for the entry
            $configKey = $config->ConfigKey;

            // take substring from the first  _    (config key format:  APP_)
            $key = strtolower(strstr($configKey, '_'));

            // make key as class property and set value
            $configObj->{$key} = $config->Value;

        }

        return $configObj;

    }


    public function setConfig($key, $value)
    {
        $select = $this->select();
        $select->where('ConfigKey = ?',$key);
        $row = $this->fetchRow($select);
     
        $row->Value = $value;
        $row->save();
        
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

