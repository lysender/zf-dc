<?php

class Dc_Validate_MaxValue extends Zend_Validate_Abstract
{

    const NOT_MAXVALUE = 'notMaxValue';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MAXVALUE => "'%value%' is greater than '%max%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'max' => '_max'
    );

    /**
     * Maximum value
     *
     * @var mixed
     */
    protected $_max;

    /**
     * Sets validator options
     *
     * @param  mixed $max
     * @return void
     */
    public function __construct($max)
    {
        if ($max instanceof Zend_Config) {
            $max = $max->toArray();
        }

        if (is_array($max)) {
            if (array_key_exists('max', $max)) {
                $max = $max['max'];
            } else {
                // require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("Missing option 'max'");
            }
        }

        $this->setMax($max);
    }

    /**
     * Returns the max option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return Zend_Validate_LessThan Provides a fluent interface
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is less than max option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        if ($value > $this->_max) {
            $this->_error(self::NOT_MAXVALUE);
            return false;
        }
        return true;
    }
}
