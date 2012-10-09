<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       // $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        //Zend_Debug::dump($userInfo);exit;
        //$userInfo->

        $auth = Zend_Auth::getInstance();
        //Zend_Debug::dump($auth->getIdentity());exit;

        //get all users
        $users = Application_Model_DbTable_User::getInstance()->getAll();
        //Zend_Debug::dump($users);exit;

        $page = $this->_getParam('page', 1);
        $this->view->page = $page;
        $paginator = Zend_Paginator::factory($users);

        $paginator->setItemCountPerPage(10);


        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;

        //  current page for redirecting
        $this->view->redirectPage = $paginator->getCurrentPageNumber();

        $ItemsPerPage = $paginator->getItemCountPerPage();
        $postCount = ($page - 1) * $ItemsPerPage;
        $this->view->postCounter = $postCount;
                          
    }
    
    public function signinAction()
    {


        //get config
        $config =  Zend_Registry::getInstance()->get('config');        
        $this->view->fbAppId = $config->_fb_appid;
        
     
    }

    public function signoutAction()
    {
        // Get an authenticator instance
        $auth = Zend_Auth::getInstance();

        $auth->clearIdentity();
        
        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        $this->_redirect($config->_resource_path.'/user/signin');


    }

    public function authAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $token = $this->getRequest()->getParam('token');

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        //facebook  authentication

        $adapter = new Auth_Adapter_Facebook($token);

        // Get an authenticator instance
        $auth = Zend_Auth::getInstance();

        // This call will automatically redirect to facebook with the passed parameters.
        $result = $auth->authenticate($adapter);

        if ( $result->isValid() ) {

            $messages= $result->getMessages();
            

            $fbUserId = $messages['user']->id;

            $fbEmail = $messages['user']->email;


            // validator for user status
            $validator = new Zend_Validate_Db_RecordExists(array(
                                            'table' => 'user',
                                            'field' => 'FbUserId',
                                            'exclude' => array(
                                                         'field' => 'Status',
                                                         'value' => 0 )
                                            )
                                        );

            //check if the user is active
            if(!$validator->isValid($fbUserId)) {

               echo "authentication failed!";return;
                                
            }

           

            // facebook user authentication with our database

            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

            $authAdapter->setTableName('user')
                        ->setIdentityColumn('FbUserId')
                        ->setCredentialColumn('FbEmail');
                        


           // pass to the adapter the fb user id
            $authAdapter->setIdentity($fbUserId)
                        ->setCredential($fbEmail);
       
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);

            if ( $result->isValid() ) {

                $userInfo = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();
                $authStorage->write($userInfo);
                
                //save access token
                Application_Model_DbTable_User::getInstance()->saveToken($userInfo->Id, $token);

                // redirect to post page
                $this->_redirect($config->_resource_path.'/post');

        }else
        {           
            echo "authentication failed!";
        }


        }else
        {
            echo "Failed!";exit;

        }



    }

    public function addAction()
    {

        $userForm =  new  Application_Form_User();
        $this->view->userForm = $userForm;

        if($this->getRequest()->isPost() && $userForm->isValid($this->getRequest()->getParams())) {

            $params = $this->getRequest()->getParams();
          
            //make a user data array from post params
            $userData = array('fbUserId' => $params['fbUserId'],'fbUserEmail' => $params['fbUserEmail']);
            Application_Model_DbTable_User::getInstance()->saveUser($userData);
            $this->view->msg = "Successfully added a new user";
            
        }
        

        
    }

     public function switchAction()
    {

        $this->_helper->viewRenderer->setNoRender();

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        $id = $this->getRequest()->getParam('id');
        $page = $this->getRequest()->getParam('page');
        // swtich user status
        Application_Model_DbTable_User::getInstance()->switchUser($id);

        $this->_redirect($config->_resource_path.'/user/index/page/'.$page);

    }



    public function fbtabAction()
    {


         //$this->_helper->viewRenderer->setNoRender();
         $this->_helper->layout->disableLayout();

         $request = $this->getRequest()->getParams();

         $app_id = "302509013139826";

         $canvas_page = "http://neofbapp.com/user/fbtab/";

         $auth_url = "http://www.facebook.com/dialog/oauth?client_id="
               . $app_id . "&redirect_uri=" . urlencode($canvas_page)."&scope=email";


         if(isset ($request['signed_request'])) {

             $signed_request = $request['signed_request'];

             list($encoded_sig, $payload) = explode('.', $signed_request, 2);

             $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
             //Zend_Debug::dump($data);
             $userTab = Application_Model_DbTable_Tab::getInstance()->getTab($data);
             //Zend_Debug::dump($userTab->tabHtml);exit;
             $this->view->pageData = $data;
             $this->view->tabHtml = stripslashes($userTab->tabHtml);
             $this->view->pageId = $data['page']['id'];
             
             

             
         }


         if(isset ($request['pageId'])) {
         
             $pageData = array();
             $pageData['pageId'] = $request['pageId'] ;
             $pageData['tab'] = $request['tab'] ;
             //Zend_Debug::dump($pageData);exit;
             Application_Model_DbTable_Tab::getInstance()->saveTab($pageData);
             

             //Zend_Debug::dump($pageData);
                          
         }



    }

    public function fblandAction()
    {
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $request = $this->getRequest()->getParams();


       $app_id = "302509013139826";
       $appSecret = "63d05c3c6a1b459e4c00c24cb9184feb";

       $canvas_page = "http://neofbapp.com/user/fbland";


       //authentication process

       if(isset ($request['code'])) {
             
             $code = $request['code'];

             // get access token


             $fbTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$app_id."&redirect_uri=".$canvas_page."&client_secret=".$appSecret."&code=".$code;

             //$response = file_get_contents(urlencode($fbTokenUrl));


             // create  request object
             $client = new Zend_Http_Client($fbTokenUrl);

             // make the request
             $response = $client->request();


             $params = null;
             parse_str($response->getBody(), $params);

             $fbUrl = "https://graph.facebook.com/me?access_token=".$params['access_token'];
             // create  request object
             $client = new Zend_Http_Client($fbUrl);

             // make the request
             $response = $client->request();

             $user = json_decode($response->getBody(), true);

            Application_Model_DbTable_Tab::getInstance()->saveUser($user['id']);
            $this->_redirect('http://apps.facebook.com/livingplanet/');

         }





       if(isset ($request['signed_request'])) {

           $signed_request = $request['signed_request'];
           $auth_url = "http://www.facebook.com/dialog/oauth?client_id="
               . $app_id . "&redirect_uri=" . urlencode($canvas_page)."&scope=email";

           list($encoded_sig, $payload) = explode('.', $signed_request, 2);

           $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
  

           if (empty($data["user_id"])) {

              echo("<script> top.location.href='" . $auth_url . "'</script>");
           } else {
              echo ("Welcome User: " . $data["user_id"]);
                            Zend_Debug::dump($data);
              echo ("<br> <a href =http://facebook.com/add.php?api_key={$app_id}&pages>Create Custom Tab</a>");
       }


      }


    }


}