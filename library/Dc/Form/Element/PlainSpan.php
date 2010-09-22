<?php

/** 
 * Simple span elemenet
 * 
 * @author Leonel
 *
 */
class Dc_Form_Element_PlainSpan extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'plainSpan';
    
    protected $_ignore = true;
}
