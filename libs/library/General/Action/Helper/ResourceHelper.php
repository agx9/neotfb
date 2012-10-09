<?php

class General_Action_Helper_ResourceHelper extends Zend_Controller_Action_Helper_Abstract
{


    public function preDispatch()
    {
        $view = $this->getActionController()->view;

        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();

        //get config
        $config = Application_Model_DbTable_Config::getInstance()->getConfig();

        $resourcePath = $config->_resource_path;
                

                
        $view->assign('resourcePath', $resourcePath);
    }
}