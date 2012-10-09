<?php

class Application_Model_DbTable_Friend extends Zend_Db_Table_Abstract
{

    protected $_name = 'fb_friends';

    /**
    * Class instance
    * @var Application_Db_Table_Friend
    */
    private static $_instance;




    /**
     * Function to get all friends
     */
    public function getAll($userId = null)
    {

        $select = $this->select();
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        //echo $select;exit;
        return $this->fetchAll($select);


    }


    public function getAllActiveFriends($userId = null)
    {
        $select = $this->select();
        $select->where('Status = 1');
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        return $this->fetchAll($select);
    }



    public function getFriends($userId, $limit = null, $offset = 0)
    {
        $select= $this->select();
        $select->where('UserId = ?', $userId);

        //limit is set
        if(isset($limit)) {

            $select->limit($limit, $offset);
        }

        return $this->fetchAll($select);


    }

        /**
     * Function to save a friend
     * @param <array> $data
     */
    public function saveFriend($data, $userId = null)
    {
        $select = $this->select();
        $select->where('FbId = ?',$data['id']);

        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);

        $row = $this->fetchRow($select);

         //if it does not then create it
        if(!$row) {
             $row = $this->createRow();
             $row->Status  = 1;
        }
        $row->FbId = $data['id'];
        $row->Name    = $data['name'];
        $row->UserId  = $userId;
        $row->save();

    }


    public function search($FriendName, $userId = null)
    {
        $select = $this->select();

        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);

        $select->where('Name Like ?', '%'.$FriendName.'%');

        return $this->fetchAll($select);
    }


  /**
   * Fuction to get class instance
   * @return Application_Db_Table_Friend
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

