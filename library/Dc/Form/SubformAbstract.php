<?php

class Dc_Form_SubformAbstract extends Dc_Form_Abstract
{
    /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;
    
    /** 
     * When multiple subforms are added many times, they are usually named
     * and suffixed with an index. This is the index used
     * 
     * @var int
     */
    protected $_subformNumericIndex;
    
    /** 
     * Indicates if the subform can be empty
     * 
     * If set as true, it runs the normal validations
     * If set as false and the subform is empty, it skip validations
     * 
     * If used as multiple indexed subforms, the rule is applied on the first subform
     * (index == 0)
     * 
     * @var boolean
     */
    protected $_allowSubformEmpty = false;
    
	/** 
	 * Initialization
	 * 
	 * @param mixed $options
	 */
	public function __construct($options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
		elseif ($options instanceof Zend_Config)
		{
			$this->setConfig($options);
		}
		
		$this->addElementPrefixPath('Dc', 'Dc');
		$this->addElementPrefixPath('Dc_Form', 'Dc/Form');
		
		Zend_Controller_Action_HelperBroker::addPrefix('Dc_Helper'); 
		
		// Extensions...
		$this->init();
		
		$this->initValidationMessages();
		
		$this->loadDefaultDecorators();
	}

	/** 
	 * Sets the numeric index to identify this form among the rest of
	 * the subforms with the same name
	 * 
	 * @param int $index
	 */
	public function setNumericIndex($index)
	{
		$this->_subformNumericIndex = $index;
		
		return $this;
	}
	
    /**
     * Load the default decorators
     *
     * @return Zend_Form_SubForm
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements');
        }
        return $this;
    }
    
    /** 
     * Validates the subform
     * 
     * @see Zend_Form::isValid()
     * @param mixed $data
     * @return boolean
     */
    public function isValid($data)
    {    	
    	if ( ! $this->_allowSubformEmpty)
    	{
    	    // Treat as valid if we are not on the first index
    		if ($this->_subformNumericIndex > 0 && $this->_subformNumericIndex !== null)
    		{
    			if ($this->isSubformEmpty())
    			{
    				// Pass because only the first subform is asserted
    				// as not empty
    				return true;
    			}
    		}
    	}
    	else
    	{
    		// If it allows the form to be empty, we will check first
    		// if it is indeed empty before skipping
    		if ($this->isSubformEmpty())
    		{
    			return true;
    		}
    	}
    	
    	// Otherwise, lets validate
    	return parent::isValid($data);
    }
    
	/** 
	 * Returns true if the subform values are all empty
	 * Because form values are string in nature. values
	 * are evaluated as string whatever it takes
	 * 
	 * @return boolean
	 */
	public function isSubformEmpty()
	{
		$values = $this->getValues();
		
		foreach ($values as $value)
		{
			if (is_array($value))
			{
				foreach ($value as $inner)
				{
					if ( ! empty($inner) || $inner === '0')
					{
						return false;
					}
				}
			}
			else if ( ! empty($value) || $value === '0')
			{
				return false;
			}
		}
		
		return true;
	}
}