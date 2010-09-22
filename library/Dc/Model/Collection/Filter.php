<?php

/** 
 * Filter collection object
 * Contains Zend_Filter_Interface compatible
 * filter objects
 * 
 * @package Dc_Model
 * @author Lysender <dc.eros@gmail.com>
 */
class Dc_Model_Collection_Filter extends Dc_Model_Collection_Abstract
{
	protected $_type = 'filter';

	protected $_prefix = 'Zend_Filter_';

	protected $_interface = 'Zend_Filter_Interface';
}

