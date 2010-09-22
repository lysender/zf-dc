<?php

/** 
 * Flag field - indicates a status or flag
 * Values allowed are 0 and 1 only
 *
 * Boolean could be the proper name to use,
 * however, we are not saving true and false
 * values, instead we save 0 for false and
 * 1 for true
 */
class Dc_Model_Field_Flag extends Dc_Model_Field_Integer
{
	/** 
	 * Auto int filter is true by default since
	 * we only need zero and one
	 *
	 * @var boolean
	 */
	protected $_autoIntFilter = true;

	/**
	 * @var string
	 */
	protected $_defaultViewHelper = 'formCheckbox';

	/** 
	 * Validates if the value is zero or one
	 *
	 * Although by theory we only need to validate
	 * zero or one, we will still allow other validators
	 * for some reason
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		$value = $this->filter();
		if ($value !== 0 && $value !== 1)
		{
			$message = $this->getModel()
				->getMessageTemplate($this->_fieldName, 'flag');
			if ($message === null)
			{
				$message = 'Allowed values are 0 or 1 only';
			}

			$this->getModel()
				->setMessage($this->_fieldName, $message);
			return false;
		}

		return parent::isValid();
	}
}
