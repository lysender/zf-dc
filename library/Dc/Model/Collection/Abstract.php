<?php

/** 
 * Collection object for Validators, Filters and ViewHelpers
 * 
 * @package Dc_Model
 * @author Lysender <dc.eros@gmail.com>
 */
abstract class Dc_Model_Collection_Abstract
{
	const TYPE_VALIDATOR 		= 'validator';
	const TYPE_FILTER 			= 'filter';
	const TYPE_VIEWHELPER 		= 'viewHelper';

	/** 
	 * @var string
	 */
	protected $_type;

	/** 
	 * @var string
	 */
	protected $_prefix;

	/** 
	 * Interface name so that only valid objects gets in
	 *
	 * @var string
	 */
	protected $_interface;

	/** 
	 * Format: array(
	 * 		'Full_Class_name' => array(
	 * 			'object'			=> object,
	 * 			'options'			=> array(
	 * 				'optionKey			=> 'value',
	 * 				...
	 * 			),
	 * 			...,
	 * 		),
	 * 		...
	 * 	);
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Reference to the field object where this collection is attacehed
	 *
	 * @var Dc_Model_Field_Interface
	 */
	protected $_field;

	/** 
	 * Returns the collection class prefix
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}

	/** 
	 * Sets the collection class prefix
	 *
	 * @param string $prefix
	 * @return $this
	 */
	public function setPrefix($prefix)
	{
		$this->_prefix = $prefix;
		return $this;
	}

	/** 
	 * Returns the collection's class interface
	 *
	 * @return string
	 */
	public function getInterface()
	{
		return $this->_interface;
	}

	/** 
	 * Sets the class interface
	 *
	 * @param string $interface
	 * @return $this
	 */
	public function setInterface($interface)
	{
		$this->_interface = $interface;
		return $this;
	}

	/**
	 * Returns the class' full name based on its type
	 *
	 * $class maybe a short name ex:
	 * 		Alnum for validator type = Zend_Validate_Alnum
	 *
	 * If no full name is found, original class is returned, however, if
	 * the original class does not exists, null is returned
	 *
	 * @param string $class
	 * @return string
	 */
	public function getClass($class)
	{
		if (class_exists($class, true))
		{
			return $class;
		}
		
		$fullname = $this->_prefix . ucfirst($class);
		
		if (class_exists($fullname, true))
		{
			return $fullname;
		}
		
		return null;
	}

	/** 
	 * Inserts a node at the beginning of the node stack
	 *
	 * @param mixed $node
	 * @param array $params
	 * @return $this
	 */
	public function prepend($node, array $params = array())
	{
		// save the current node stack
		$stack = $this->_data;
		// clear it
		$this->_data = array();
		// insert new node
		$this->set($node, $params);
		// inserts back the original stack
		$this->_data += $stack;

		return $this;
	}

	/** 
	 * Appends the node to the node stack
	 * Original behavior of set appends to the node
	 * stack so append is a more alias to set
	 *
	 * @param mixed $node
	 * @param array $params
	 * @return $this
	 */
	public function append($node, array $params = array())
	{
		return $this->set($node, $params);
	}

	/** 
	 * Sets node (adds/replaces)
	 * Node can either be a string or object
	 *
	 * If string, it can be a short or full name
	 *
	 * For consistency, saved index is the class full name
	 *
	 * @param mixed $node
	 * @param array $params
	 * @return $this
	 */
	public function set($node, array $params = array())
	{
		$class = '';
		if ($node instanceof $this->_interface)
		{
			$class = get_class($node);
			$params['object'] = $node;
		}
		elseif (is_string($node))
		{
			$class = $this->getClass($node);
		}
		else
		{
			throw new Dc_Model_Exception('Invalid '.$this->_type.' given');
		}

		$this->_data[$class] = $params;

		return $this;
	}

	/** 
	 * Returns true if the node name exists
	 * The node name accepts short class name
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has($name)
	{
		$class = $this->getClass($name);
		return array_key_exists($class, $this->_data);
	}

	/** 
	 * Returns the node object
	 * Node name can either be a short or full name
	 *
	 * @param string $node
	 * @return Object
	 */
	public function get($node)
	{
		$class = $this->getClass($node);

		if (array_key_exists($class, $this->_data))
		{
			if (isset($this->_data[$class]['object']))
			{
				return $this->_data[$class]['object'];
			}

			// otherwise, create the object along with the node options
			$options = null;
			if (isset($this->_data[$class]['options']))
			{
				$options = $this->_data[$class]['options'];
			}

			// make sure no parameter is passed when there are not options
			if ($options === null)
			{
				$this->_data[$class]['object'] = new $class;
			}
			else
			{
				$this->_data[$class]['object'] = new $class($options);
			}

			// set the view renderer if the the object is a view helper
			if ($this->_type == self::TYPE_VIEWHELPER)
			{
				$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
				$this->_data[$class]['object']->setView($viewRenderer->view);
			}
			return $this->_data[$class]['object'];
		}

		return null;
	}

	/** 
	 * Sets the node parameter
	 * 
	 * @param string $node
	 * @param string $param
	 * @param mixed $paramValue
	 * @return $this
	 */
	public function setParam($node, $param, $paramValue)
	{
		$class = $this->getClass($node);
		if (!$this->has($class))
		{
			return $this;
		}
		$this->_data[$class][$param] = $paramValue;
		return $this;
	}

	/** 
	 * Returns true if the node parameter exists
	 *
	 * @param string $node
	 * @param string $param
	 * @return boolean
	 */
	public function hasParam($node, $param)
	{
		$class = $this->getClass($node);
		if (!array_key_exists($class, $this->_data))
		{
			return false;
		}

		return array_key_exists($param, $this->_data[$class]);
	}

	/** 
	 * Removes a node parameter
	 *
	 * @param string $node
	 * @param string $param
	 * @return $this
	 */
	public function removeParam($node, $param)
	{
		$class = $this->getClass($node);
		if ($this->hasParam($class, $param))
		{
			unset($this->_data[$class][$param]);
		}
		return $this;
	}

	/** 
	 * Returns the node parameter
	 *
	 * @param string $node
	 * @param string $param
	 * @return boolean
	 */
	public function getParam($node, $param)
	{
		$class = $this->getClass($node);
		if ($this->hasParam($class, $param))
		{
			return $this->_data[$class][$param];
		}
		return null;
	}

	/** 
	 * Returns all node objects as an array
	 *
	 * @return array
	 */
	public function getAll()
	{
		$keys = array_keys($this->_data);
		$validators = array();

		foreach ($keys as $key)
		{
			$validators[$key] = $this->get($key);
		}

		return $validators;
	}

	/** 
	 * Unsets a node entry
	 * Node name can either be a short class name or 
	 * a full name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function remove($name)
	{
		$class = $this->getClass($name);
		if (array_key_exists($class, $this->_data))
		{
			unset($this->_data[$class]);
		}
		return $this;
	}
}

