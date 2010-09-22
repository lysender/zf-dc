<?php

/** 
 * View helper to render a simple span
 * Enter description here ...
 * @author Leonel
 *
 */
class Dc_View_Helper_PlainSpan extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'span' tag.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function plainSpan($name, $value = null, $attribs = null)
    {
        $xhtml = '<span'
        		. $this->_htmlAttribs($attribs)
        		. '>'
        		. $this->view->escape($value)
                . '</span>';

        return $xhtml;
    }
}
