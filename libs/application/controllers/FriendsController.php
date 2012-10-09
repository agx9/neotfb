<?php

class FriendsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

        //get config
        $config = Application_Model_DbTable_Config::getInstance()->getConfig();

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        $page = $this->_getParam('page', 1);
        $this->view->page = $page;

        //get search query
        $search = $this->_getParam('search');
        if($search) {
            $friends = Application_Model_DbTable_Friend::getInstance()->search($search, $userId);
            // search query
            $this->view->search = trim($search);
        } else
        {
            //Get all friends
            $friends = Application_Model_DbTable_Friend::getInstance()->getAll($userId);

        }

        $paginator = Zend_Paginator::factory($friends);
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
        $this->_helper->viewRenderer->setNoRender();


        // Get facebook access token
        $token = $this->getRequest()->getParam('token');

        //get config
        $config =  Zend_Registry::getInstance()->get('config');

        //get user
        $auth = Zend_Auth::getInstance();

        //get user id
        $userId = $auth->getIdentity()->Id;

        // making fb request url for fetching user groups
        $fbUrl = "https://graph.facebook.com/me/friends?access_token=".$token."&fields=name,email,id";

        // create  request object
        $client = new Zend_Http_Client($fbUrl);

        // make the request
        $response = $client->request();
        //echo $response->getBody();exit;
        //json_decode(file_get_contents($fbUrl));exit;

        // decoding the json response
        $friends = json_decode($response->getBody(), true);

        //Zend_Debug::dump($friends);exit;



        //Zend_Debug::dump($groups);exit;
        //echo $groups[0]->name;exit;
        foreach ($friends['data'] as $friend) {


            //save the group
            Application_Model_DbTable_Friend::getInstance()->saveFriend($friend, $userId);


        }

        $this->_redirect($config->_resource_path.'/friends');

    }


}



