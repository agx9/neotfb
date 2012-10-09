<?php

class Application_Model_DbTable_PostLog extends Zend_Db_Table_Abstract
{

    protected $_name = 'post_log';

    /**
    * Class instance
    * @var Application_Db_Table_PostLog
    */
    private static $_instance;


    /**
     * Function to save friend's wall post log
     * @param  array $friend
     * @param  array $post
     * @param  int   $userId
     * @param  int   $status
     */
    public function savelog($friend, $post, $userId, $status)
    {
        
        $row = $this->createRow();
        
        $row->PostId = $post['Id'];
        $row->FbFriendId    = $friend['FbId'];
        $row->UserId  = $userId;
        $row->Status = $status;
        $row->save();

        
    }


    /**
     * Function to get last log bu user id & post
     * @param <type> $userId
     * @param <type> $postId
     * @return <type> 
     */
    public function getNoOfLoggedByUserAndPost($userId, $postId)
    {
        $select = $this->select();
        $select->where('UserId = ?', $userId)
               ->where('PostId = ?', $postId);
        $result = $this->fetchAll($select);

        return(count($result));
        
                      
        
    }



  /**
   * Fuction to get class instance
   * @return Application_Db_Table_PostLog
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

