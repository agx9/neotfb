<?php

class PostController extends Zend_Controller_Action
{

    /**
     * Access token for cronjob
     * @var <String> 
     */
    private $_token = '495858577484934';

    public function init()
    {
        header( 'Content-Type: text/html; charset=utf-8' );

    }

    public function indexAction()
    {
        
         //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $page = $this->_getParam('page', 1);
        $this->view->page = $page;

        //get search query
        $search = $this->_getParam('search');
        if($search) {
            $posts = Application_Model_DbTable_Post::getInstance()->search($search, $userId);

            // search query
            $this->view->search = trim($search);
        } else
        {

            //get all posts
            $posts = Application_Model_DbTable_Post::getInstance()->getAll($userId);

        }


        $delete  = $this->getRequest()->getParam('delete');

        //if submit via deactivate
        if($this->getRequest()->isPost() && isset($delete) ) {

            $params = $this->getRequest()->getPost();

            

            //update groups' status
            Application_Model_DbTable_PostQueue::getInstance()->deletePosts($params);

            $this->_redirect($config->_resource_path.'/post');
            

        }






        $paginator = Zend_Paginator::factory($posts);

        $paginator->setItemCountPerPage(20);


        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;

//        $pageNumber = $this->paginator->getCurrentPageNumber();
        $ItemsPerPage = $paginator->getItemCountPerPage();
        $postCount = ($page - 1) * $ItemsPerPage;
        $this->view->postCounter = $postCount;
    }

    public function editAction()
    {
        
        //get config
        $config =  Zend_Registry::getInstance()->get('config');
        
         //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        // get the post form
        $postForm = new  Application_Form_Post();
        $this->view->postForm = $postForm;
        $id = null;

        $id = $this->getRequest()->getParam('id');
        if(isset($id)) {            
            //get post by id
            $post = Application_Model_DbTable_Post::getInstance()->get($id, $userId);
            $this->view->post = $post->Title;
                       // Zend_Debug::dump(html_entity_decode($post->Title, ENT_COMPAT, 'UTF-8'));exit;
            $postForm->getElement('title')->setValue($post->Title);
            $postForm->getElement('content')->setValue($post->Post);
            $postForm->getElement('image')->setValue($post->Image);
            $this->view->image = $post->Image;
            $postForm->image->setRequired(false);

        }
        if($this->getRequest()->isPost() && $postForm->isValid($this->getRequest()->getParams())) {

            //get params
            $params = $this->getRequest()->getParams();

                                   
            //create file transfer adapter
            $adapter = new Zend_File_Transfer_Adapter_Http();

            //get image file info
            $file = $adapter->getFileInfo();             
            
            //set destination
            $adapter->setDestination('uploads/');

            // make an array with post params
            $post = array('Title' => $params['title'],'Post' => $params['content'],'Image' => $adapter->getFileName(),'Status' => 0);

            //add validation
            //$adapter->addValidator('MimeType', false, array('image/gif', 'image/jpeg','image/png'));
            
            //$adapter->addPrefHYKixPath('file', $adapter->getDestination());
            

            if (!$adapter->receive()) {
                $messages = $adapter->getMessages();
                if(Application_Model_DbTable_Post::getInstance()->savePost($post,$id) && isset($id))
                   $this->view->msg = "Post successfully saved..";
                echo implode("\n", $messages);
            }else
            {
                // save post
                if(Application_Model_DbTable_Post::getInstance()->savePost($post,$id))
                {
                   $this->view->msg = "Post successfully saved..";
                   $this->_redirect($config->_resource_path.'/post');
                }
                else
                   $this->view->msg = "Something went wrong, Operation failed!";
                
            }


        }

    }

    public function inserttoqAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        //get post id 
        $postId = $this->getRequest()->getParam('id');

        // get all active groups
        $groups = Application_Model_DbTable_PostQueue::getInstance()->insertToQueue($postId);
                
        
    }


    /**
     * action to send posts as emails to the fb groups
     * @return <type>
     */
    public function sendmailAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        //get accesss token
        $token = $this->getRequest()->getParam('token');

        if($token != $this->_token) {

            echo "Access Denied!";
            return;

        }


        //$config = Application_Model_DbTable_Config::getInstance()->getConfig();
        $posts = Application_Model_DbTable_PostQueue::getInstance()->sendMail();
        //Zend_Debug::dump($config);exit;


        
    }


    public function sendpostsAction()
    {
        set_time_limit('2000000');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        //get accesss token
        $token = $this->getRequest()->getParam('token');

        if($token != $this->_token) {

            echo "Access Denied!";
            return;

        }

        Application_Model_DbTable_PostQueue::getInstance()->sendPosts();

        
        
    }

    public function errorAction()
    {

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $failedGroups = Application_Model_DbTable_PostQueue::getInstance()->getFailed($userId);
        //Zend_Debug::dump($failedGroups);exit
        $this->view->errors = $failedGroups;
        
        
    }


    public function wallpostAction()
    {
        set_time_limit('2000000');
        $this->_helper->viewRenderer->setNoRender();
        //$this->_helper->layout->disableLayout();

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

         //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        //get post id
        $postId = $this->_getParam('postid');

        //get  no of friends
        $noOfFriends = $this->_getParam('friends');

        

        $loggedCount = Application_Model_DbTable_PostLog::getInstance()->getNoOfLoggedByUserAndPost($userId, $postId);

        $accessToken = $auth->getIdentity()->AccessToken;

        //get friends by limit and offset
        $friends = Application_Model_DbTable_Friend::getInstance()->getFriends($userId, $noOfFriends, $loggedCount);
        
        //get post row by id
        $post = Application_Model_DbTable_Post::getInstance()->get($postId);     

        if($post) {
            foreach ($friends as $friend) {

                $result = Application_Model_DbTable_Post::getInstance()->sendToWall($post, $friend, $accessToken, $userId );
                //Zend_Debug::dump($auth->getIdentity()->AccessToken);exit;

                if($result == true)
                    echo '<a href="https://facebook.com/'.$friend->FbId.'">'.$friend->Name .'</a>  <span style="color:green;"> Success</span><br/><br/>';
                else
                    echo '<a href="https://facebook.com/'.$friend->FbId.'">'.$friend->Name .'</a>  <span style="color:red;"> Failed</span><br/><br/>';


            }
        }else {

            echo 'Error: invalid post';
        }
       
        

        
    }

    public function towallAction()
    {
        //get post id
        $postId = $this->_getParam('postid');

        $this->view->postId = $postId;



        
    }





}





