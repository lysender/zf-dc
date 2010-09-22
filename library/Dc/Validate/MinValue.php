<?php

class Dc_Validate_MinValue extends Zend_Validate_Abstract
{

    const NOT_MINVALUE = 'notMinValue';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MINVALUE => "'%value%' is less than '%min%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min'
    );

    /**
     * Minimum value
     *
     * @var mixed
     */
    protected $_min;

    /**
     * Sets validator options
     *
     * @param  mixed $min
     * @return void
     */
    public function __construct($min)
    {
        if ($min instanceof Zend_Config) {
            $min = $min->toArray();
        }

        if (is_array($min)) {
            if (array_key_exists('min', $min)) {
                $min = $min['min'];
            } else {
                // require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Missing option 'min'");
            }
        }

        $this->setMin($min);
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  mixed $min
     * @return Zend_Validate_GreaterThan Provides a fluent interface
     */
    public function setMin($min)
    {
        $this->_min = $min;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is greater than min option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ($value < $this->_min) {
            $this->_error(self::NOT_MINVALUE);
            return false;
        }
        return true;
    }
}
