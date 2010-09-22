<?php

/** 
 * Validator collection object
 * Contains Zend_Validate_Interface compatible
 * validator objects
 * 
 * @package Dc_Model
 * @author Lysender <dc.eros@gmail.com>
 */
class Dc_Model_Collection_Validator extends Dc_Model_Collection_Abstract
{
	protected $_type = 'validator';

	protected $_prefix = 'Zend_Validate_';

	protected $_interface = 'Zend_Validate_Interface';
}
