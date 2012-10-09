<?php

class Application_Form_User extends Zend_Form
{


    public function init()
    {
        $this->setMethod('post');

        // create form elements

        //for fb user id
        $fbUserId = $this->createElement('text', 'fbUserId');
        $fbUserId->setLabel('Facebook User Id:');
        $fbUserId->setRequired(TRUE);
        $fbUserId->removeDecorator('HtmlTag');
        $fbUserId->addValidator(new Zend_Validate_Db_NoRecordExists('user', 'FbUserId'), TRUE );


        // for fb email
        $fbUserEmail = $this->createElement('text', 'fbUserEmail');
        $fbUserEmail->setLabel('Facebook User Email:');
        $fbUserEmail->setRequired(TRUE);
        $fbUserEmail->addValidator('EmailAddress', true);
        $fbUserEmail->removeDecorator('HtmlTag');
        $fbUserEmail->addValidator(new Zend_Validate_Db_NoRecordExists('user', 'FbEmail'), TRUE );
            
        
        
        $this->addElement($fbUserId);
        $this->addElement($fbUserEmail);

        $this->addElement('submit', 'save', array('label' => 'Add'));



    }

}

