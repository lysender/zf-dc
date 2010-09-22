<?php

abstract class Dc_Form_Abstract extends Zend_Form
{
	/** 
	 * Validation messages
	 * 
	 * @var array
	 */
	protected $_messageConfig;
	
	/** 
	 * Token field name to use - anti CSRF
	 * 
	 * @var string
	 */
	protected $_tokenField = 'token';
	
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

		// Initialize plugins and custom decorators
		$this->addElementPrefixPath('Dc', 'Dc');
		$this->addElementPrefixPath('Dc_Form', 'Dc/Form');
		$this->addElementPrefixPath('Dc_View', 'Dc/View');
		
		// Anti CSRF token
		$token = new Zend_Form_Element_Hash('token');
		$token->setSalt('12, 25, Av, 5t, tasda212')
			->setIgnore(true)
			->setTimeout(600)
			->setDecorators(array(
			array('ViewHelper')
		));
		
		$this->addElement($token);
		
		// Extensions...
		$this->init();
		
		$this->initValidationMessages();
		
		$this->loadDefaultDecorators();
	}
	
	/** 
	 * Overriden by the child classes to load the messages
	 * from a config file 
	 * 
	 * @return array
	 */
	public function loadMessageConfig()
	{
		// Implemented by child classes
	}
	
	public function initValidationMessages()
	{
		$messages = $this->loadMessageConfig();
		
		if ( ! empty($messages))
		{
			foreach ($messages as $field => $fieldMessages)
			{
				foreach ($fieldMessages as $validator => $validatorMessages)
				{
					$element = $this->getElement($field);
					$validate = null;
					
					if ($element)
					{
						$validate = $element->getValidator($validator);
					}
					
					if ($validate)
					{
						$validate->setMessages($validatorMessages);
					}
				}
			}
		}
		
		return $this;
	}
	
	/** 
	 * Returns the message config
	 * 
	 * @return Zend_Config
	 */
	public function getMessageConfig()
	{
		if ($this->_messageConfig === null)
		{
			$this->_messageConfig = $this->loadMessageConfig();;
		}
		
		return $this->_messageConfig;
	}
	
	/** 
	 * Ses the message config for error messages
	 * 
	 * @param array $config
	 * @return $this
	 */
	public function setMessageConfig($config)
	{
		$this->_messageConfig = $config;
		
		return $this;
	}
	
	/** 
	 * Returns the first error as an array
	 * The first element is the field and the second element
	 * is the message
	 * 
	 * @param array $messages
	 * @return array
	 */
	public function getFirstError(array $messages = array())
	{
		if (empty($messages))
		{
			$messages = $this->getMessages();
		}
		
		if ( ! empty($messages))
		{
			$keys = array_keys($messages);
			$field = reset($keys);
			
			$message = reset($messages[$field]);
			
			if ($field == $this->_tokenField)
			{
				$message = 'Session time out, try again';
			}
			
			return array(
				'field'	=> $field, 
				'message' => $message
			);
		}
		
		return false;
	}
	
	/** 
	 * Converts a multi dimensional array of messages into
	 * a single dimensional array with only one message per field
	 * 
	 * @param array $messages
	 * @return array
	 */
	public function flattenMessages(array $messages = array())
	{
		if (empty($messages))
		{
			$messages = $this->getMessages();
		}
		
		$result = array();
		foreach ($messages as $field => $fieldMessages)
		{
			// Only one message per field
			$message = reset($fieldMessages);
			
			if ($field == $this->_tokenField)
			{
				$message = 'Session timeout, try again';
			}
			
			$result[$field] = $message;
		}
		
		return $result;
	}
}