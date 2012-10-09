<?php

class GroupController extends Zend_Controller_Action
{

    public function init()
    {
        header( 'Content-Type: text/html; charset=utf-8' );
    }

    public function indexAction()
    {       

        //get config
        $config = Application_Model_DbTable_Config::getInstance()->getConfig();

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $switch  = $this->getRequest()->getParam('deactivate');

        //if submit via deactivate
        if($this->getRequest()->isPost() && isset($switch) ) {

            $params = $this->getRequest()->getPost();

            //update groups' status
            Application_Model_DbTable_Group::getInstance()->switchGroups($params, $userId);


            
        }


        $page = $this->_getParam('page', 1);
        $this->view->page = $page;

        //get search query
        $search = $this->_getParam('search');
        if($search) {
            $groups = Application_Model_DbTable_Group::getInstance()->search($search, $userId);
            // search query
            $this->view->search = trim($search);
        } else
        {
            //Get all groups
            $groups = Application_Model_DbTable_Group::getInstance()->getAll($userId);
            
        }

        $paginator = Zend_Paginator::factory($groups);
        $paginator->setItemCountPerPage($config->_page_no_of_rows_in_table);


        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;

        $ItemsPerPage = $paginator->getItemCountPerPage();
        $postCount = ($page - 1) * $ItemsPerPage;
        $this->view->postCounter = $postCount;
        
        $this->view->redirectPage = $paginator->getCurrentPageNumber();
        
        $this->view->fbAppId = $config->_fb_appid;


    }

    public function getAction()
    {
        
        set_time_limit('200');
        // Get facebook access token
        $token = $this->getRequest()->getParam('token');

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        // making fb request url for fetching user groups
        $fbUrl = "https://graph.facebook.com/me/groups?access_token=".$token."&fields=name,email,id";
        
        // create  request object
        $client = new Zend_Http_Client($fbUrl);

        // make the request
        $response = $client->request();
        //echo $response->getBody();exit;
        //json_decode(file_get_contents($fbUrl));exit;

        // decoding the json response
        $groups = json_decode($response->getBody(), true);




        //Zend_Debug::dump($groups);exit;
        //echo $groups[0]->name;exit;
        foreach ($groups['data'] as $group) {

            
            //save the group
            Application_Model_DbTable_Group::getInstance()->saveGroup($group, $userId);
                        

        }

        $this->_redirect($config->_resource_path.'/group');

        
    }

    public function switchAction()
    {

        $this->_helper->viewRenderer->setNoRender();

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $id = $this->getRequest()->getParam('id');
        $page = $this->getRequest()->getParam('page');        
        // swtich group status
        Application_Model_DbTable_Group::getInstance()->switchGroup($id, $userId);
                
        $this->_redirect($config->_resource_path.'/group/index/page/'.$page);
                
    }

    public function updatelistAction()
    {

        $this->_helper->viewRenderer->setNoRender();
        
        //get auth session
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        //get access token
        $token = $auth->getIdentity()->AccessToken;

        //get all groups
        $groups = Application_Model_DbTable_Group::getInstance()->getAll($userId);

        foreach($groups as $group) {

            
            //prepare fql query to check user's group
            $fql = 'select gid, uid from group_member where gid ='.$group->GroupId. ' and uid =me()';
            

            // making graph api request url
            $fbUrl = "https://graph.facebook.com/fql?q=".urlencode($fql)."&access_token=".$token;

            // create  request object
            $client = new Zend_Http_Client($fbUrl);

            // make the request
            $response = $client->request();
        
            // decoding the json response
            $groupInfo = json_decode($response->getBody(), true);

            //if got a successful response
            if($response->isSuccessful()){
                
                //if no data == that group is no more valid for current user
                if(count($groupInfo['data']) == 0)
                   Application_Model_DbTable_Group::getInstance()->deleteGroup($group->GroupId, $userId);

                
            }




        }

        $this->_redirect($config->_resource_path.'/group');


    }


}

