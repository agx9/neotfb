<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


    protected function _initRoutes()
    {
        // Create a router
//
//       $router = Zend_Controller_Front::getInstance()->getRouter(); // returns a rewrite router by default
//       $router->addRoute(
//                          'queue',
//                           new Zend_Controller_Router_Route('post/inserttoq/:post/:group',
//                           array('controller' => 'post',
//                                 'action' => 'inserttoq'))
//);


    }

    protected function _initGlobalConfigs()
    {
      $config = $this->getOptions();
      $dbConfig = new Zend_Config($config["database"]);            
      $db = Zend_Db::factory($dbConfig);
      Zend_Db_Table::setDefaultAdapter($db);
      Zend_Registry::getInstance()->set("dbconfig", $db);

      if($this->_logQueries)
      {
         $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
         $profiler->setEnabled(true);
         $db->setProfiler($profiler);
      }

//get config
        $config = Application_Model_DbTable_Config::getInstance()->getConfig();
        Zend_Registry::getInstance()->set('config', $config);
//      //DB::getConfigGlobals();
//      //Zend_Registry::getInstance()->set("config", $configs);
//
//      Zend_Registry::getInstance()->set('siteInfo', $configs->site);
//      Zend_Registry::getInstance()->set('path', 	 $configs->path);
//      $this->_globalConfigs = $configs;
//
//      $view = $this->bootstrap("view")->getResource("view");
//      $view->assign('appstage', APPSTAGE);
   }


   protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new Auth_Action_Helper_Auth()
        );

        Zend_Controller_Action_HelperBroker::addHelper(
            new General_Action_Helper_ResourceHelper()
        );
    }





}

