<?php

class Dc_Validate_StringNotEmpty extends Zend_Validate_Abstract
{
    const IS_STRING_EMPTY = 'isStringEmpty';
    
    /**
    * @var array
    */
    protected $_messageTemplates = array(
        self::IS_STRING_EMPTY => "Ang string ay empty"
    );
    
    /**
    * Defined by Zend_Validate_Interface
    *
    * Returns true if and only if $value is not an empty value.
    *
    * @param  string $value
    * @return boolean
    */
    public function isValid($value)
    {
        if (is_string($value) && ($value == ''))
        {
            $this->_error(self::IS_STRING_EMPTY);
            return false;
        }
        
        return true;
    }
}