<?php

class Application_Model_DbTable_Post extends Zend_Db_Table_Abstract
{

    protected $_name = 'fb_posts';

    /**
    * Class instance
    * @var Application_Db_Table_Post
    */
    private static $_instance;

    public function get($id, $userId = null)
    {
        $select = $this->select();
        $select->where('Id = ?',$id);

        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);


        return $this->fetchRow($select);
                
    }

    
        /**
     * Function to get all posts
     */
    public function getAll($userId = null)
    {
        $select = $this->select();
        
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        
        return $this->fetchAll($select);
               
    }


    /**
     * Function to search posts
     * @param <String> $groupName
     * @return <type>
     */
    public function search($postTitle, $userId = null)
    {
        $select = $this->select();
        
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        
        $select->where('Title Like ?', '%'.$postTitle.'%');
        return $this->fetchAll($select);
    }


    /**
     * Function to save a post
     * @param <array> $post
     * @return <bool> 
     */
    public function savePost($post,$id=NULL)
    {
         //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $select = $this->select();

        $row = null;

        if($id) {
            $select->where('Id = ?',$id);
            $select->where('UserId = ?', $userId);
            $row = $this->fetchRow($select);
        }
        
         //if it does not then create it
        if(!$row) {
             $row = $this->createRow();
        }
        $row->Title  = $post['Title'];
        $row->Post   = $post['Post'];
        $row->UserId = $userId;
        
        // on edit & save situation user may not upload a new image
        //so checking it
        if(!empty($post['Image']))
            $row->Image  = $post['Image'];
        
        $row->Status = $post['Status'];
        //$row->Time  = DateTime();
        if($row->save())
             return true;        
        
    }


    /**
     * Function to delete a post
     * @param <int> $postId
     */
    public function deletePost($postId)
    {
        
        $where=array('Id = ?'=> $postId);
        $this->delete($where);

    }

    /**
     * function to  post to friends' wall
     */
    public function sendToWall($post, $friend, $accessToken, $userId )
    {

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        // making fb request url for posting to friends'wall
        $fbUrl = "https://graph.facebook.com/".$friend->FbId."/feed"."?access_token=".$accessToken;
        
        // create  request object
        $client = new Zend_Http_Client($fbUrl);

        $client->setParameterPost('message', $post->Post);
        $client->setParameterPost('picture', rtrim($config->_url,"/").'/'.$post->Image);


        // make the request
        $response = $client->request('POST');

        
        //if successful
        if($response->isSuccessful()){

            Application_Model_DbTable_PostLog::getInstance()->savelog($friend, $post, $userId, 1);
            $result = true;
            
            

        }else {

            Application_Model_DbTable_PostLog::getInstance()->savelog($friend, $post, $userId, 0);
            $result = false;
        }

        return $result;

                

        

        

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

