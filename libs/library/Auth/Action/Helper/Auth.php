<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Signup
 *
 * @author anurag
 */
class Auth_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract {
    
    protected  $view;

    public function preDispatch()
    {
      
     

    }

    public function init()
    {
        
        $view = $this->getView();
        $request = $this->getRequest()->getParams();
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $auth = Zend_Auth::getInstance();

        //get config
        $config =  Zend_Registry::getInstance()->get('config');
        

        //Zend_Debug::dump($auth->hasIdentity());exit;
        if(!$auth->hasIdentity()) {

              Zend_Layout::getMvcInstance()->getView()->__set('loggedIn', false);
              if(($request['controller']!='user' || $request['action'] != 'signin') &&
                       ($request['controller']!='user' || $request['action']!='auth') && 
                             ($request['controller']!='post' || $request['action']!='sendmail') &&
                                    ($request['controller']!='post' || $request['action']!='sendposts'))
                                   {
                  header('Location: '.$config->_resource_path.'/user/signin');
                  //$this->getActionController()->getHelper('redirector')->gotoUrl('user/signin');
              }

        }else {
           Zend_Layout::getMvcInstance()->getView()->__set('loggedIn', true);

        }
        
//$this->getActionController()->view->loggedin =  'hi';
                //$this->getView()->__set('loggedin', 'hi');
    }




    
    public function getViewww() {

        if(null == $this->view) {

            return $this->view;
        }

       $controller  = $this->getActionController();
       $view =  $controller->view;
       return $view;
    }




    function getView()
    {
    
    $vr = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    return $vr->initView();

    }



}

