<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';

    /**
    * Class instance
    * @var Application_Db_Table_User
    */
    private static $_instance;


    /**
     * Function to get all users
     * @return <rows>
     */
    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll();

    }


    /**
     * Function to save a user
     * @param <array> $userData
     */
    public function saveUser($userData)
    {
        $select = $this->select();
        $row = $this->createRow();
        $row->FbUserId = $userData['fbUserId'];
        $row->FbEmail  = $userData['fbUserEmail'];
        $row->Status   = 1;
        $row->save();

    }

    public function saveToken($userId, $token)
    {
        $select = $this->select();
        $select->where('id = ?', $userId);
        $row = $this->fetchRow($select);
        $row->AccessToken = $token;
        $row->save();
                
    }

    /**
     * Function to to switch user status
     * @param <int> $id
     */
    public function switchUser($id)
    {
        $select = $this->select();
        $select->where('id = ?', $id);
        $row = $this->fetchRow($select);

        if((int)$row->Status == 0)
            $row->Status = 1;
        else
            $row->Status = 0;

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