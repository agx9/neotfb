<?php

class Application_Model_DbTable_PostQueue extends Zend_Db_Table_Abstract
{

    protected $_name = 'post_queue';/**
    * Class instance
    * @var Application_Db_Table_PostQueue
    */
    private static $_instance;


    /**
     * Function to get postqueue entries for email posting  by limit
     * @param <int> $limit
     * @return <roeset> Application_Db_Table_PostQueue
     */
    public function getPostsForEmailPosting($limit)
    {
          $select = $this->select();
          $select->from(array('pq' => $this->_name))
                 ->setIntegrityCheck(FALSE);

          $select->join(array('g'=> 'fb_groups'),'g.GroupId = pq.GroupId',array('Email'));
          $select->join(array('p'=> 'fb_posts'),'p.Id = pq.PostId',array('Title','Post','Image'));
          $select->where('pq.Status = 0');
          $select->where('g.Status = 1');
          $select->limit($limit);     
          return $this->fetchAll($select);

        
    }


    /**
     * Function to get postqueue entries   by limit
     * @param <int> $limit
     * @return <roeset> Application_Db_Table_PostQueue
     */
    public function getPosts($limit)
    {
          $select = $this->select();
          $select->from(array('pq' => $this->_name))
                 ->setIntegrityCheck(FALSE);

          
          $select->join(array('p'=> 'fb_posts'),'p.Id = pq.PostId',array('Title','Post','Image'));
          $select->join(array('u'=> 'user'),'u.Id = pq.UserId',array('AccessToken'));
          $select->where('pq.Status = 0');
          //$select->where('g.Status = 1');
          $select->limit($limit);    // echo $select;exit;
          return $this->fetchAll($select);


    }


    public function setStatus($id,$status)
    {
        $select = $this->select();
        $select->where('id = ?',$id);
        $row = $this->fetchRow($select);       
        $row->Status = $status;
        $row->save();

        
    }

    /**
     * Function to check a post is in queue
     * @param <int> $id
     * @return <String>
     */
    public function isInQueue($id)
    {
        $validator = new Zend_Validate_Db_RecordExists('post_queue', 'PostId');
        if($validator->isValid($id)) {
            

             $postCount = Application_Model_DbTable_PostQueue::getInstance()->getPostCount($id, 0);

             if($postCount > 0)
                 return "Q";
             else
                 return "P";

        }
        
        
    }


    public function getPostCount($id, $status)
    {
        $select = $this->select();
        $select->where('PostId = ?',$id)
               ->where('Status = ?',$status);
        $rows = $this->fetchAll($select);        
        return count($rows);


        
    }

    /**
     * Function to insert posts
     * @param <int> $postId
     */
    public function insertToQueue($postId)
    {


        //get user session
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;


        // get all active groups
        $groups = Application_Model_DbTable_Group::getInstance()->getAllActiveGroups($userId);
        //Zend_Debug::dump($groups);exit;
        foreach($groups as $group) {

            $row = $this->createRow();
            $row->PostId  = $postId;
            $row->GroupId = $group->GroupId;
            $row->Status  = 0;
            $row->UserId  = $userId;
            $row->save();

        }

       echo "Success!";

        
    }




    public function sendMail()
    {

       //get config
       $config = Application_Model_DbTable_Config::getInstance()->getConfig();

       //get posts
       $posts = Application_Model_DbTable_PostQueue::getInstance()->getPostsForEmailPosting($config->_sendmail_postcount);
       


        // gmail smtp login info

        $emailConfig = array('auth' => 'login',
                'username' => base64_decode($config->_gmail),
                'password' => base64_decode($config->_gmailpassword),
                'port' => '587',
                'ssl' => 'tls');
        //'neotribe201220122012'

       
                

       //$transport = new Zend_Mail_Transport_Smtp('smtp.googlemail.com', $config);
       $smtpHost = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $emailConfig);
       
       
       
       if(count($posts) > 0) {

           foreach ($posts as $post) {
               $mail = new Zend_Mail('utf-8');
               echo $post->Email;//echo file_get_contents($post->Image);exit;
               $mail->setBodyText(html_entity_decode($post->Post, ENT_COMPAT, 'UTF-8'));
       
               $mail->setFrom(base64_decode($config->_gmail), 'Anurag RS');
               
               $imageData = pathinfo($post->Image); //Zend_Debug::dump($imageData['extension']);exit;
               $mail->addTo($post->Email);
               $mail->createAttachment(file_get_contents($post->Image), 'image/'.$imageData['extension'], Zend_Mime::DISPOSITION_INLINE , Zend_Mime::ENCODING_BASE64, 1);
               //$mail->addTo('anuragmdt+33@gmail.com');
               
               //echo ' image/'.$imageData['extension'];exit;


               $mail->setSubject(htmlentities($post->Title, ENT_COMPAT, 'UTF-8'));
               $mail->send($smtpHost);
               //update post status as sent
               $this->setStatus($post->Id, 1);
               
           }
       
       
       }else
       {
           echo "Queue is empty!";

       }



    }


    /**
     * Function to post fb groups
     * using as a cronjob
     */
    public function sendPosts()
    {
         //get config
         $config = Application_Model_DbTable_Config::getInstance()->getConfig();

         //get posts
         $posts = Application_Model_DbTable_PostQueue::getInstance()->getPosts($config->_sendmail_postcount);

         // Get an authenticator instance
         //$auth = Zend_Auth::getInstance();

         // get user data
         //$user = $auth->getIdentity();


         //fb access token
         //$token = $user->AccessToken;

         if(count($posts) > 0) {

            $postCount  = 0;
            $errorCount = 0;
            foreach ($posts as $post) {

                // get group id
                $groupId = $post->GroupId;

                // making fb request url for posting to user groups
                $fbUrl = "https://graph.facebook.com/".$groupId."/feed"."?access_token=".$post->AccessToken;
       
                // create  request object
                $client = new Zend_Http_Client($fbUrl);

                $client->setParameterPost('message', $post->Post);
                $client->setParameterPost('picture', rtrim($config->_url,"/").'/'.$post->Image);


                // make the request
                $response = $client->request('POST');

                //if successful
                if($response->isSuccessful()){

                    //echo $response->getBody();exit;
                  
                    //update post status
                    $this->setStatus($post->Id, 1);
                    //increment successfull post count
                    $postCount++;

                }
                else {

                    $this->setStatus($post->Id, 2);
                    echo $post->GroupId.'<br/>';
                    //increment error count
                    $errorCount++;
                }
                                                                
                                                                          

            }

            echo 'Successfull : '.$postCount.'<br/> Errors : '.$errorCount;


         }else
         {
             echo "Queue is empty!";

         }



    }
    
    public function getFailed($userId = null)
    {

        $select = $this->select();
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        
        $select->where('Status = ?', 2)
               ->order('PostId');

        return $this->fetchAll($select);
    }




    public function deletePosts($params)
    {
       // Zend_Debug::dump($params);exit;

        foreach($params as $post=>$value) {

            // if the param is not submit
            if($post != 'delete') {

                $where=array('PostId = ?'=> $post);
                $this->delete($where);
                Application_Model_DbTable_Post::getInstance()->deletePost($post);
            }
        }

        
    }

    /**
    * Fuction to get class instance
    * @return Application_Db_Table_PostQueue
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

