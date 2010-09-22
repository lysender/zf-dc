<?php

/** 
 * Base class for Dc_Model fields
 *
 * @package Dc_Model
 * @author Lysender <dc.eros@gmail.com>
 */
class Dc_Model_Field implements Dc_Model_Field_Interface
{
	const VALIDATOR_PREFIX 		= 'Zend_Validate_';
	const FILTER_PREFIX 		= 'Zend_Filter_';
	const VIEW_HELPER_PREFIX 	= 'Zend_View_Helper_';
	
	const TYPE_VALIDATOR 		= 'validator';
	const TYPE_FILTER 			= 'filter';
	const TYPE_VIEWHELPER 		= 'viewHelper';

	/** 
	 * Field name used by the model to identify this field
	 *
	 * Model field key
	 *
	 * @var string
	 */
	protected $_fieldName;
	/**
	 * @var Dc_Model_ValidatorCollection
	 */
	protected $_validatorCollection;

	/** 
	 * @var Dc_Model_FilterCollection
	 */
	protected $_filterCollection;

	/** 
	 * @var Dc_Model_ViewHelperCollection
	 */
	protected $_viewHelperCollection;

	/** 
	 * @var mixed
	 */
	protected $_value;

	/** 
	 * Default value for this field
	 * Used when the field's value is null
	 *
	 * @var mixed
	 */
	protected $_defaultValue;

	/** 
	 * Choices for this field
	 * Used in conjunction with select elements
	 *
	 * @var array
	 */
	protected $_choices = array();

	/** 
	 * Reference to the model object where this field is attached
	 *
	 * @var Dc_Model_Abstract
	 */
	protected $_model;

	/** 
	 * Name of the field as the model sees it
	 * This is because the field does not know anything about the model
	 *
	 * @var string
	 */
	protected $_name;

	/** 
	 * Indicates that this field is either mapped to data source or not
	 * Unmapped fields are used in forms for other purposes such as security
	 * or for usability purposes in input forms
	 *
	 * Sample applications are form tokens or date select fields
	 * where date is selected via year/month/day on multiple input elements
	 *
	 * @var boolean
	 */
	protected $_mapped = true;

	/** 
	 * Indicates that the field is either the field is required or optional
	 * If true, the field is required and an automatic validator NotEmprt is added
	 * If false, the field is optional and validations are not run when empty
	 *
	 * @bar boolean
	 */
	protected $_required = false;

	/** 
	 * Indicates that this field is converted to null when
	 * the value is considered empty
	 *
	 * @var boolean
	 */
	protected $_nullWhenEmpty = false;

	/** 
	 * The name of the field that this field is dependent of
	 * If the field that this field is dependent already has
	 * errors, then this field is not validated anymore
	 *
	 * Usefull in cases when a field to be validated is using
	 * other fields to perform its validation.
	 *
	 * Example cases are password and confirm_password fields
	 * where confirm_password is validated only when password
	 * is valid.
	 *
	 * Another example is when a multiple field represents a 
	 * single mapped field (ex: date for year/month/day),
	 * when the year is invalid, then there is no need to
	 * validate the month and day because the dependent field
	 * is already invalid
	 *
	 * @var string
	 */
	protected $_dependent;

	/** 
	 * The default view helper that is automatically inserted
	 * When calling the input() method
	 *
	 * Only Zend_View_Helper_* are allowed
	 *
	 * @var string
	 */
	protected $_defaultViewHelper;

	/** 
	 * __construct()
	 *
	 * @param array $options
	 * @return void
	 */
	public function __construct(array $options = null)
	{
		if (is_array($options) && !empty($options))
		{
			$this->_loadFieldProperties($options);
		}

		// allow child classes to initialize the field for their own
		$this->init();
	}

	/** 
	 * Initialization method to be implemented by child classes
	 *
	 * @return void
	 */
	public function init(){}

	/** 
	 * Loads the property for this fields
	 *
	 * @param array $options
	 * @return $this
	 */
	protected function _loadFieldProperties(array $options)
	{
		// initialize collections
		$opts = array(
			self::TYPE_VALIDATOR,
			self::TYPE_FILTER,
			self::TYPE_VIEWHELPER
		);

		foreach ($options as $key => $val)
		{
			// set property if the key is a property
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method))
			{
				$this->$method($val);
			}
			else
			{
				// if it is a collection, load it
				$optKey = substr($key, 0, -1);
				if (in_array($optKey, $opts))
				{
					$this->_loadCollectionData($optKey, $val);
				}
			}
		}

		return $this;
	}

	/** 
	 * Loads the collection data to its destination collection
	 *
	 * @param string $type
	 * @param array $data
	 * @return $this
	 */
	protected function _loadCollectionData($type, array $data)
	{
		$method = 'get' . ucfirst($type) . 'Collection';
		$col = $this->$method();

		foreach ($data as $node => $params)
		{
			$col->set($node, $params);
		}

		return $this;
	}

	/** 
	 * Returns the model this field is attached
	 *
	 * @return Dc_Model_Abstract
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/** 
	 * Sets the reference to the model object this field is attached
	 *
	 * @param Dc_Model_Abstract
	 * @return $this
	 */
	public function setModel(Dc_Model_Abstract &$model)
	{
		$this->_model = $model;
		return $this;
	}

	/** 
	 * Sets the field name
	 *
	 * Field name is the key used by the model
	 * to identify this field
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setFieldName($name)
	{
		$this->_fieldName = $name;
		return $this;
	}

	/** 
	 * Returns the field name
	 *
	 * @return string
	 */
	public function getFieldName()
	{
		return $this->_fieldName;
	}

	/** 
	 * Sets the field's name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}

	/** 
	 * Returns the field's name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/** 
	 * Sets the mapped flag
	 *
	 * @param boolean $mapped
	 * @return $this
	 */
	public function setMapped($mapped)
	{
		$this->_mapped = (boolean)$mapped;
		return $this;
	}

	/** 
	 * Returns true if the field is mapped
	 *
	 * @return boolean
	 */
	public function isMapped()
	{
		return $this->_mapped;
	}

	/** 
	 * Sets the nullWhenEmptyFlag
	 *
	 * @param boolean $flag
	 * @return $this
	 */
	public function setNullWhenEmpty($flag)
	{
		$this->_nullWhenEmpty = (boolean)$flag;
		return $this;
	}

	/** 
	 * Return true when the field is converted to null
	 * when value is considered empty
	 *
	 * @return boolean
	 */
	public function isNullWhenEmpty()
	{
		return $this->_nullWhenEmpty;
	}

	/** 
	 * Sets the field this field is dependent of
	 * 
	 * @param string $field
	 * @return $this
	 */
	public function setDependent($field)
	{
		$this->_dependent = $field;
		return $this;
	}

	/** Returns the field this field is dependent
	 *
	 * @return string
	 */
	public function getDependent()
	{
		return $this->_dependent;
	}

	/** 
	 * Returns the value for this field
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		if ($this->isEmpty() && $this->isNullWhenEmpty())
		{
			$this->_value = null;
		}

		return $this->_value;
	}

	/** 
	 * Sets the value for the current field
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->_value = $value;
		return $this;
	}

	/** 
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		// default value is not modified by other flags and is
		// returned as is
		return $this->_defaultValue;
	}

	/** 
	 * Sets the default value for the current field
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function setDefaultValue($value)
	{
		$this->_defaultValue = $value;
		return $this;
	}

	/** 
	 * Sets that required flag that either the field
	 * is required/true or optinal/false
	 *
	 * @param boolean $flag
	 * @return $this
	 */
	public function setRequired($flag)
	{
		$this->_required = (boolean)$flag;
		return $this;
	}

	/** 
	 * Returns true if the field is required and false when optional
	 *
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->_required;
	}

	/** 
	 * Sets the default view helper name
	 *
	 * @param string $viewHelper
	 * @return $this
	 */
	public function setDefaultViewHelper($viewHelper)
	{
		$this->_defaultViewHelper = $viewHelper;
		return $this;
	}

	/** 
	 * Returns the name of the default view helper
	 * Not the object
	 *
	 * @return string
	 */
	public function getDefaultViewHelper()
	{
		return $this->_defaultViewHelper;
	}

	/** 
	 * Returns the choices for this field
	 *
	 * @return array
	 */
	public function getChoices()
	{
		return $this->_choices;
	}

	/** 
	 * Sets the choices for the current field
	 *
	 * @param array $choices
	 * @return $this
	 */
	public function setChoices(array $choices)
	{
		$this->_choices = $choices;
		return $this;
	}
	/** 
	 * Returns true if the field value is considered empty
	 * null and empty string is considered as empty
	 * whereas 0 or '0' is not empty
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		$val = $this->_value;
		if ($val !== 0 && $val!== '0' && trim($val) == '' && empty($val))
		{
			return true;
		}
		return false;
	}

	/** 
	 * Returns true if the field value is valid for the given validators
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		// if not required and is empty, no checking is done
		if (!$this->isRequired() && $this->isEmpty())
		{
			return true;
		}
		$vcol = $this->getValidatorCollection();

		// if required and notEmpty validator is not set, then set it
		if ($this->isRequired())
		{
			if (!$vcol->has('NotEmpty'))
			{
				$vcol->set('NotEmpty', array(
					'options' => array('type' => Zend_Validate_NotEmpty::STRING)
				));
			}
		}

		$validators = $this->getValidatorCollection()->getAll();
		$value = $this->filter();
		foreach ($validators as $v => $obj)
		{
			if (!$obj->isValid($value))
			{
				$messages = $obj->getMessages();
				$messages = reset($messages);

				$this->getModel()->setMessage($this->_name, $messages);

				return false;
			}
		}

		return true;
	}

	/** 
	 * Filters the field's value, sets the value from the filtered value
	 * and lastly returns the filtered value
	 *
	 * @return mixed
	 */
	public function filter()
	{
		$filters = $this->getFilterCollection()->getAll();
		$value = $this->getValue();

		foreach ($filters as $f => $obj)
		{
			$value = $obj->filter($value);
		}

		$this->setValue($value);
		return $value;
	}

	/** 
	 * Returns the HTML string for the default view helper
	 * Null is returned when default view helper is not specified
	 *
	 * @param string $name
	 * @param array $attribs
	 * @return string
	 */
	public function input($name, array $attribs = null)
	{
		// check first if the default view helper is set
		$viewHelper = $this->getDefaultViewHelper();
		if ($viewHelper === null)
		{
			return null;
		}

		// auto insert when not yet added to collection
		$vhcol = $this->getViewHelperCollection();
		if (!$vhcol->has($viewHelper))
		{
			$vhcol->prepend($viewHelper);
		}

		return $this->view($name, $viewHelper, $attribs);
	}

	/** 
	 * Returns the HTML string for the field's view helper
	 *
	 * @param string $name
	 * @param string $viewHelper
	 * @param array $attribs
	 * @return string
	 */
	public function view($name, $viewHelper, array $attribs = null)
	{
		$vhcol = $this->getViewHelperCollection();
		$view = $vhcol->get($viewHelper);

		if ($view === null)
		{
			return null;
		}

		// override name when property name exists
		$propName = $this->getName();
		if ($propName !== null && $propName != $name)
		{
			$name = $propName;
		}

		// override default value when property value is set
		$value = $this->getDefaultValue();
		$propValue = $this->getValue();
		if ($propValue !== null && $propValue != $value)
		{
			$value = $propValue;
		}

		// load attribs from this function merged with collection
		if (empty($attribs))
		{
			$attribs = array();
		}

		if ($vhcol->hasParam($viewHelper, 'attribs'))
		{
			$attribs += $vhcol->getParam($viewHelper, 'attribs');
		}

		// choices for form selects
		$choices = null;
		if ($viewHelper == 'formSelect' || $viewHelper == 'formRadio')
		{
			$propChoices = $this->getChoices();
			if (!empty($propChoices))
			{
				$choices = $propChoices;
			}

			if ($viewHelper == 'formRadio')
			{
				// check if list separator is set on param
				$listsep = null;
				if ($vhcol->hasParam($viewHelper, 'listsep'))
				{
					$listsep = $vhcol->getParam($viewHelper, 'listsep');
				}

				// load list seprator for radio
				if (isset($attribs['listsep']))
				{
					$listsep = $attribs['listsep'];
					unset($attribs['listsep']);
				}

				// do not pass listsep when it is not set
				if ($listsep === null)
				{
					return $view->$viewHelper($name, $value, $attribs, $choices);
				}
				else
				{
					return $view->$viewHelper($name, $value, $attribs, $choices, $listsep);
				}
			}
		}
		
		// special cases for checkboxes
		if ($viewHelper == 'formCheckbox')
		{
			// values should be 0/1, true/false
			if ($value)
			{
				$attribs += array('checked' => 'checked');
			}
			// then change the value to always 1
			$value = 1;
		}
		return $view->$viewHelper($name, $value, $attribs, $choices);
	}

	/** 
	 * Returns the validator collection object
	 *
	 * @return Dc_Model_Collection_Validator
	 */
	public function getValidatorCollection()
	{
		if ($this->_validatorCollection === null)
		{
			$this->_validatorCollection = new Dc_Model_Collection_Validator;
		}

		return $this->_validatorCollection;
	}

	/** 
	 * Sets the validator collection for this field
	 *
	 * @param Dc_Model_Collection_Validator
	 * @return $this
	 */
	public function setValidatorCollection(Dc_Model_Collection_Validator $validators)
	{
		$this->_validatorCollection = $validators;
		return $this;
	}

	/** 
	 * Returns the filter collection object
	 *
	 * @return Dc_Model_Collection_Filter
	 */
	public function getFilterCollection()
	{
		if ($this->_filterCollection === null)
		{
			$this->_filterCollection = new Dc_Model_Collection_Filter;
		}

		return $this->_filterCollection;
	}

	/** 
	 * Sets the filter collection for this field
	 *
	 * @param Dc_Model_Collection_Filter
	 * @return $this
	 */
	public function setFilterCollection(Dc_Model_Collection_Filter $filters)
	{
		$this->_filterCollection = $filters;
		return $this;
	}

	/** 
	 * Returns the view helper collection object
	 *
	 * @return Dc_Model_Collection_ViewHelper
	 */
	public function getViewHelperCollection()
	{
		if ($this->_viewHelperCollection === null)
		{
			$this->_viewHelperCollection = new Dc_Model_Collection_ViewHelper;
		}

		return $this->_viewHelperCollection;
	}

	/** 
	 * Sets the view helper collection for this field
	 *
	 * @param Dc_Model_Collection_ViewHelper
	 * @return $this
	 */
	public function setViewHelperCollection(Dc_Model_Collection_ViewHelper $viewHelpers)
	{
		$this->_viewHelperCollection = $viewHelpers;
		return $this;
	}
}
