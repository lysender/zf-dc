<?php

class Dc_Model_Field_Integer extends Dc_Model_Field
{
	/** 
	 * Determines the minimum value for this field
	 * Adds a min value validator
	 *
	 * @var int
	 */
	protected $_minValue;

	/** 
	 * Determinies the maximum value for this field
	 * Adds a max value validator 
	 *
	 * @var int
	 */
	protected $_maxValue;

	/** 
	 * @var string
	 */ 
	protected $_defaultViewHelper = 'formText';

	/** 
	 * When set to true, will add an int filter to this field
	 *
	 * @var boolean
	 */
	protected $_autoIntFilter = false;

	/** 
	 * Sets the minimum value for this field
	 *
	 * @param int $min
	 * @return $this
	 */
	public function setMinValue($min)
	{
		$min = (int)$min;
		if ($this->_maxValue !== null && $min > $this->_maxValue)
		{
			throw new Dc_Model_Exception('Min value must be always less than max value');
		}

		$this->_minValue = $min;
		return $this;
	}

	/** 
	 * Returns the min value
	 *
	 * @return int
	 */
	public function getMinValue()
	{
		return $this->_minValue;
	}

	/** 
	 * Returns the max value
	 * 
	 * @param int $max
	 * @return $this
	 */
	public function setMaxValue($max)
	{
		$max = (int)$max;
		if ($this->_minValue > $max)
		{
			throw new Dc_Model_Exception('Max must always be more than the min value');
		}

		$this->_maxValue = $max;
		return $this;
	}

	/** 
	 * Returns the max value
	 *
	 * @return int
	 */
	public function getMaxValue()
	{
		return $this->_maxValue;
	}

	/** 
	 * Sets the autoint filter flag
	 *
	 * @param boolean $flag
	 * @return $this
	 */
	public function setAutoIntFilter($flag)
	{
		$this->_autoIntFilter = (boolean)$flag;
		return $this;
	}

	/** 
	 * Returns the autoinf filter status
	 *
	 * @return boolean
	 */
	public function isAutoIntFilter()
	{
		return $this->_autoIntFilter;
	}

	/** 
	 * Overrides the base filter method by adding
	 * the int filter when set and when it is not yet in
	 * the filter collection
	 *
	 * @return mixed
	 */
	public function filter()
	{
		if ($this->_autoIntFilter)
		{
			$fcol = $this->getFilterCollection();
			if (!$fcol->has('Int'))
			{
				$fcol->prepend('Int');
			}
		}

		return parent::filter();
	}

	/** 
	 * Overrides the base isValid method by adding
	 * LessThan and GreaterThan validators and the mandatory
	 * Int validator
	 *
	 * Returns true if and only if the field has passed
	 * all the validator rules
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		$vcol = $this->getValidatorCollection();
		if (!$vcol->has('Int'))
		{
			$vcol->prepend('Int');
		}

		// LessThan and GreaterThan validators because what we need
		// is minimum and maximum, although Between can be used,
		// it is only applicable when both minValue
		// and maxValue are set
		
		// error messages are defined inline but can be overriden
		// by model message template

		// validate min value

		$value = $this->filter();
		if ($this->_minValue !== null)
		{
			if ($value < $this->_minValue)
			{
				$message = $this->getModel()
					->getMessageTemplate($this->_fieldName, 'minValue');
				if ($message === null)
				{
					$message = 'Minimum value is ' . $this->_minValue
						. ', but value is lesser';
				}

				$this->getModel()
					->setMessage($this->_fieldName, $message);
				return false;
			}
		}

		// validate max value
		if ($this->_maxValue !== null)
		{
			if ($value > $this->_maxValue)
			{
				$message = $this->getModel()
					->getMessageTemplate($this->_fieldName, 'maxValue');
				if ($message === null)
				{
					$message = 'Maximum value is ' . $this->_maxValue
					   . ', but value is greater';
				}

				$this->getModel()
					->setMessage($this->_fieldName, $message);
				return false;
			}
		}
		return parent::isValid();
	}
}
