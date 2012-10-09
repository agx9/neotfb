<?php

class Application_Form_Post extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');

        // create form elements

        $title = $this->createElement('text', 'title');
        $title->setLabel('Title:');
        $title->setErrorMessages(array("D_STR_ERR_BUY_COUNTRY"));
        $title->setRequired(TRUE);
        //$title->removeDecorator('HtmlTag');
              //->removeDecorator('label');

        $content = $this->createElement('textarea', 'content');
        $content->setLabel('Post');
        $content->setRequired(TRUE);
        $content->removeDecorator('HtmlTag');

        $image = $this->createElement('file', 'image');
        $image->setLabel('Image:');
        $image->setRequired(TRUE);
        $image->removeDecorator('HtmlTag');         
        $image->addValidator('NotEmpty');




        $this->addElement($title);
        $this->addElement($content);
        $this->addElement($image);
        $this->addElement('submit', 'save', array('label' => 'Post'));



    }


}

