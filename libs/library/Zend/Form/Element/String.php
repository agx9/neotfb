<?php
/**
 * Interface and Implementation for SimpleDecorator class
 *
 * PHP version 5
 *
 * LICENSE: Please email info@dealised
 * for more information
 *
 * @category Dealised
 * @package  WWW
 * @author   Bob Maidens
 * @license  -
 * @link     -
 */

/**
 * Class representing 
 *
 * @category Dealised
 * @package  WWW
 * @author   Bob Maidens
 * @license  -
 * @link     -
 */

class Form_Element_String extends Zend_Form_Element_Xhtml
{
  protected $_str = "";

  static $count = 0;

  public function __construct($spec, $options = null) {
    parent::__construct("Form_Element_String".Form_Element_String::$count, $options );

    Form_Element_String::$count++;
    $this->_str = $spec;

  }
  public function render(Zend_View_Interface $view = null) {
    return $this->_str;
  }
}
