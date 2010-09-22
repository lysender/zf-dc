<?php

/** 
 * Interface for Dc_Model compatible fields
 *
 * @package Dc_Model
 * @author Lysender <dc.eros@gmail.com>
 */
interface Dc_Model_Field_Interface
{
	/** 
	 * Returns the model this field is attached
	 *
	 * @return Dc_Model_Abstract
	 */
	public function getModel();

	/** 
	 * Sets the reference to the model this field is attached
	 *
	 * @param Dc_Model_Abstract $model
	 * @return $this
	 */
	public function setModel(Dc_Model_Abstract &$model);

	/** 
	 * Returns the value for the current field
	 *
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Sets the value for the current field
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function setValue($value);

	/** 
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function getDefaultValue();

	/** 
	 * Sets the default value for this field
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function setDefaultValue($value);

	/** 
	 * Returns the choices for this field
	 *
	 * @return array
	 */
	public function getChoices();

	/** 
	 * Sets the choices for this field
	 *
	 * @param array $choices
	 * @return $this
	 */
	public function setChoices(array $choices);

	/** 
	 * Returns the name of this field
	 *
	 * @return string
	 */
	public function getName();

	/** 
	 * Sets the name of the field
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name);

	/** 
	 * Sets the mapped flag
	 *
	 * @param boolean $mapped
	 * @return $this
	 */
	public function setMapped($mapped);

	/** 
	 * Returns true if the field is mapped
	 *
	 * @return boolean
	 */
	public function isMapped();

	/** 
	 * Sets the required flag that the field is either
	 * required or optional
	 *
	 * @param boolean $flag
	 * @return $this
	 */
	public function setRequired($flag);

	/** 
	 * Returns true if the field is required and false
	 * when optional
	 *
	 * @return boolean
	 */
	public function isRequired();

	/** 
	 * Sets the nullWhenEmpty flag
	 *
	 * @param boolean $flag
	 * @return $this
	 */
	public function setNullWhenEmpty($flag);

	/** 
	 * Returns the nullWhenEmpty flag
	 *
	 * @return boolean
	 */
	public function isNullWhenEmpty();

	/** 
	 * Returns the field that this field is dependent of
	 *
	 * @return string
	 */
	public function getDependent();

	/** 
	 * Sets the field that this field is dependent of
	 *
	 * @param string $field
	 * @return $this
	 */
	public function setDependent($field);

	/** 
	 * Returns true if the fields value is valid for the given validators
	 *
	 * @return boolean
	 */
	public function isValid();

	/** 
	 * Applies all filters that this field has,
	 * sets the filted value to this field's value
	 * and return the filtered value
	 *
	 * @return mixed
	 */
	public function filter();

	/** 
	 * Returns the HTML string for the fields view helper
	 * 
	 * @param string $name
	 * @param string $viewHelper
	 * @param array $attribs
	 * @return string
	 */
	public function view($name, $viewHelper, array $attribs = null);

	/** 
	 * Returns true if and only if the value is considered empty
	 * null, empty string are considered empty
	 *
	 * @return boolean
	 */
	public function isEmpty();

	/** 
	 * Returns the validator collection object
	 *
	 * @return Dc_Model_Collection_Validator
	 */
	public function getValidatorCollection();

	/** 
	 * Sets the validator collection object
	 *
	 * @param Dc_Model_Collection_Validator
	 * @return $this
	 */
	public function setValidatorCollection(Dc_Model_Collection_Validator $validators);

	/** 
	 * Returns the filter collection object
	 *
	 * @return Dc_Model_Collection_Filter
	 */
	public function getFilterCollection();

	/** 
	 * Sets the filter collection object
	 * 
	 * @param Dc_Model_Collection_Filter
	 * @return $this
	 */
	public function setFilterCollection(Dc_Model_Collection_Filter $filters);

	/** 
	 * Returns the view helper collection object
	 *
	 * @return Dc_Model_Collection_ViewHelper
	 */
	public function getViewHelperCollection();

	/** 
	 * Sets the view helper collection
	 *
	 * @param Dc_Model_Collection_ViewHelper
	 * @return $this
	 */
	public function setViewHelperCollection(Dc_Model_Collection_ViewHelper $viewHelpers);
}
