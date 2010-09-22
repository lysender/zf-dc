<?php

/** 
 * In div tight decorator, an attrib 'tightClass' must be passed
 * so that a class can be placed on the wrapping div, otherwise,
 * no class is added
 * 
 * @author Leonel
 *
 */
class Dc_Form_Decorator_DivTight extends Zend_Form_Decorator_ViewHelper
{
	/** 
	 * Name of the class that will wrap the form element
	 * 
	 * @var string
	 */	
	protected $_class;
	
    public function getElementAttribs()
    {
        if (null === ($element = $this->getElement())) {
            return null;
        }
        
        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

       	// Add a class that will highlight this element as error}
        if ($element->hasErrors())
        {
        	if (isset($attribs['class']))
        	{
        		$attribs['class'] .= ' has-error';
        	}
        	else
        	{
        		$attribs['class'] = 'has-error';
        	}
        }
        
        if (isset($attribs['tightClass']))
        {
        	$this->_class = $attribs['tightClass'];
        	unset($attribs['tightClass']);
        }
        
        if (method_exists($element, 'getSeparator')) {
            if (null !== ($listsep = $element->getSeparator())) {
                $attribs['listsep'] = $listsep;
            }
        }

        if (isset($attribs['id'])) {
            return $attribs;
        }

        $id = $element->getName();

        if ($element instanceof Zend_Form_Element) {
            if (null !== ($belongsTo = $element->getBelongsTo())) {
                $belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
                $id = $belongsTo . '-' . $id;
            }
        }

        $element->setAttrib('id', $id);
        $attribs['id'] = $id;

        return $attribs;
    }

    public function getValue($element)
    {
    	if ($element instanceof Dc_Form_Element_PlainSpan)
    	{
    		return $element->getLabel();
    	}
    	
    	return parent::getValue($element);
    }
    
    public function render($content)
    {
        $element = $this->getElement();

        $view = $element->getView();
        if (null === $view) {
            // require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('ViewHelper decorator cannot render without a registered view object');
        }

        if (method_exists($element, 'getMultiOptions')) {
            $element->getMultiOptions();
        }

        $helper        = $this->getHelper();
        $separator     = $this->getSeparator();
        $value         = $this->getValue($element);
        $attribs       = $this->getElementAttribs();
        $name          = $element->getFullyQualifiedName();
        $id            = $element->getId();
        $attribs['id'] = $id;

        $helperObject  = $view->getHelper($helper);
        if (method_exists($helperObject, 'setTranslator')) {
            $helperObject->setTranslator($element->getTranslator());
        }

        $elementContent = $view->$helper($name, $value, $attribs, $element->options);
        
        $divClass = ($this->_class) ? ' class='.$this->_class : '';
        
        $elementContent = '<div'.$divClass.'>'
        		. $elementContent
                . '</div>';
        
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }
}