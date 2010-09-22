<?php

/** 
 * Generic char field for dc modeling
 *
 */
class Dc_Model_Field_Char extends Dc_Model_Field
{
	/** 
	 * Minimum length rule for this field
	 * StringLength validator is automatically added when 
	 * this is set
	 *
	 * @var int
	 */
	protected $_minLength;

	/** 
	 * Maximum length rule for this field
	 * StringLength validator is automatically added
	 * when this is set
	 *
	 * @var int
	 */
	protected $_maxLength;

	/** 
	 * When true, StringTrim filter is automatically added
	 *
	 * @var boolean
	 */
	protected $_autoTrim = true;

	/** 
	 * When autotrim is true, this filter is used
	 *
	 * @var string
	 */
	protected $_autoTrimFilter = 'StringTrim';

	/** 
	 * Default view helper for character field is form text
	 *
	 * @var string
	 */
	protected $_defaultViewHelper = 'formText';

	/** 
	 * Sets the minimum length for this field
	 *
	 * @param int $min
	 * @return $this
	 */
	public function setMinLength($min)
	{
		if ($this->_maxLength !== null && $min > $this->_maxLength)
		{
			throw new Dc_Model_Exception('Minimum length must be less than or equal to maximum length');
		}

		// allows setting min to null to disable string length validator
		$this->_minLength = $min;

		return $this;
	}

	public function getMinLength()
	{
		return $this->_minLength;
	}

	/** 
	 * Sets the maximum length for this field
	 *
	 * @param int $max
	 * @return $this
	 */
	public function setMaxLength($max)
	{
		if ($max === null)
		{
			$this->_maxLength = null;
		}
		else if ($max < $this->_minLength)
		{
			throw new Dc_Model_Exception('Maximum length must be greater than or equal to the minimum length');
		}
		else
		{
			$this->_maxLength = $max;
		}

		return $this;
	}

	/** 
	 * Returns the maximum length for this field
	 *
	 * @return int
	 */
	public function getMaxLength()
	{
		return $this->_maxLength;
	}

	/** 
	 * Sets the auto trim status flag
	 * 
	 * @param boolean $flag
	 * @return $this
	 */
	public function setAutoTrim($flag)
	{
		$this->_autoTrim = (boolean)$flag;
		return $this;
	}

	/** 
	 * Returns true if autotrim is enabled
	 *
	 * @return boolean
	 */
	public function isAutoTrim()
	{
		return $this->_autoTrim;
	}

	/** 
	 * Sets the autotrim filter for this field
	 *
	 * @param string $filter
	 * @return $this
	 */
	public function setAutoTrimFilter($filter)
	{
		$this->_autoTrimFilter = $filter;
		return $this;
	}

	/** 
	 * Returns the auto trim filter 
	 *
	 * @return string
	 */
	public function getAutoTrimFilter()
	{
		return $this->_autoTrimFilter;
	}

	/** 
	 * Filters the value and return the filtered value
	 *
	 * This method overrides the base class method and inserts
	 * the StringTrim when autoTrim is set
	 *
	 * @return mixed
	 */
	public function filter()
	{
		if ($this->_autoTrim && $this->_autoTrimFilter)
		{
			$fcol = $this->getFilterCollection();
			if (!$fcol->has($this->_autoTrimFilter))
			{
				$fcol->prepend($this->_autoTrimFilter);
			}
		}

		return parent::filter();
	}

	/** 
	 * Returns true if the fiels value is value
	 *
	 * This method overrides the base class method and inserts
	 * the StringLength validator when minLength and maxLength
	 * is properly set
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		if ($this->_minLength !== null)
		{
			$vcol = $this->getValidatorCollection();
			if (!$vcol->has('StringLength'))
			{
				$vcol->prepend('StringLength', array(
					'options'	=> array(
						'min'		=> $this->_minLength,
						'max'		=> $this->_maxLength
					)
				));
			}
		}

		return parent::isValid();
	}
}
