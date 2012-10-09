<?php

class ConfigController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
        $this->view->config = Application_Model_DbTable_Config::getInstance()->getConfig();
  //Zend_Debug::dump(base64_decode($this->view->config->_gmailpassword));exit;
        if($this->getRequest()->isPost()) {

            $configKeys = $this->getRequest()->getParams();
             //Zend_Debug::dump($configKeys);exit;
             if($configKeys['APP_GMAIL']) {
                 //$config = Application_Model_DbTable_Config::getInstance()->
                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_GMAIL",base64_encode($configKeys['APP_GMAIL']));

             }


             if($configKeys['APP_GMAILPASSWORD']) {
                 //$config = Application_Model_DbTable_Config::getInstance()->
                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_GMAILPASSWORD",base64_encode($configKeys['APP_GMAILPASSWORD']));

             }


             // for number of mails per cron job run
             if($configKeys['APP_SENDMAIL_POSTCOUNT']) {
                 
                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_SENDMAIL_POSTCOUNT",$configKeys['APP_SENDMAIL_POSTCOUNT']);

             }

             // for fb app id
             if($configKeys['APP_FB_APPID']) {
                 
                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_FB_APPID",$configKeys['APP_FB_APPID']);

             }

             // for application url
             if($configKeys['APP_URL']) {

                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_URL",$configKeys['APP_URL']);

             }


             // for application resource path
             if($configKeys['APP_RESOURCE_PATH']) {

                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_RESOURCE_PATH",$configKeys['APP_RESOURCE_PATH']);

             }


             // for number of rows in table
             if($configKeys['APP_PAGE_NO_OF_ROWS_IN_TABLE']) {
                 
                 Application_Model_DbTable_Config::getInstance()->setConfig("APP_PAGE_NO_OF_ROWS_IN_TABLE",$configKeys['APP_PAGE_NO_OF_ROWS_IN_TABLE']);

             }



        }
        

        
    }


}
