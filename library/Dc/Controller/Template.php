<?php

/**
 * Template controller for common controller / view / javascript processing
 *
 */
abstract class Dc_Controller_Template extends Zend_Controller_Action
{
	const MESSAGE_STYLE_PLAIN = 'plain';
	const MESSAGE_STYLE_POPUP = 'popup';
	
	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	
	/**
	 * Javascript that are executed on document ready powered by jQuery
	 * A compulsory jQuery ready statement is written to view and the value of
	 * this gets into the ready function part
	 *
	 * @var string
	 */
	protected $_readyScript = '';
	
	/**
	 * Javascript that are written on header (not on document ready)
	 * These includes variable declarations and the like
	 * In the view, this is enclosed on a <script> tag
	 *
	 * @var string
	 */
	protected $_globalScript = '';
	
	/** 
	 * @var string
	 */
	protected $_previousPage;
	
	/**
	 * @var Dc_Log
	 */
	protected $_logger;
	
	/**
	 * @var Dc_Auth
	 */
	protected $_auth;
	
	/** 
	 * Message style of success / error messages
	 * 
	 * @var string
	 */
	protected $_messageStyle = self::MESSAGE_STYLE_PLAIN;
	
	/**
	 * Controller initialization
	 *
	 * @return void
	 */
	public function init()
	{
		$this->_session = new Zend_Session_Namespace('CURRENT_USER');
		
		$this->_logger = Dc_Log::getInstance();
		$this->_auth = Dc_Auth::getInstance();
	}
	
	/**
	 * Popup / alert message via javascript
	 * Optionally focus on a form element if specified
	 *
	 * @param string $message
	 * @param string $focus
	 * @return void
	 */
	public function setPopupMessage($message, $focus = null)
	{
		$script = '';
		if ($message)
		{
			$script .= 'alert("' . $message . '");'.PHP_EOL;
			if ($focus)
			{
				$script .= '$("#' . $focus . '").focus();'.PHP_EOL;
			}
			$this->_readyScript .= $script.PHP_EOL;
		}
	}
	
	/**
	 * Accepts an array messages, merge then into a single message
	 * and set popup message
	 *
	 * @param array $messages
	 * @param string $focus
	 * @return void
	 */
	public function setPopupMessages(array $messages, $focus = null)
	{
		if (!empty($messages))
		{
			$firstFocus = array_keys($messages);
			$firstFocus = reset($firstFocus);
		
			$msg = implode('\n', $messages);
			
			if ($focus)
			{
				$firstFocus = $focus;
			}
			
			$this->highLightFields($messages);
			$this->setPopupMessage($msg, $firstFocus);
		}
	}
	
	/**
	 * Highlights the fields by changing their background colors
	 *
	 * @param array $fields
	 * @return void
	 */
	public function highLightFields(array $fields)
	{
		foreach ($fields as $f => $message)
		{
			$this->_readyScript .= '$("#' . $f . '").addClass("highlight-error");'.PHP_EOL;
		}
	}
	
	/**
	 * Executes additional routines after the method
	 *
	 * @return void
	 */
	public function postDispatch()
	{
		if ( ! empty($this->_session->successMessage))
		{
			if ($this->_messageStyle == self::MESSAGE_STYLE_POPUP)
			{
				$this->setPopupMessage($this->_session->successMessage);
			}
			else
			{
				$this->view->successMessage = $this->_session->successMessage;
			}
			
			$this->_session->successMessage = null;
		}
		if ( ! empty($this->_session->errorMessage))
		{
			if ($this->_messageStyle == self::MESSAGE_STYLE_POPUP)
			{
				$this->setPopupMessage($this->_session->errorMessage);
			}
			else
			{
				$this->view->errorMessage = $this->_session->errorMessage;
			}
			
			$this->_session->errorMessage = null;
		}
		
		if ( ! empty($this->_readyScript) || ! empty($this->_globalScript))
		{
			$scripts = '
				%s
				$(function(){
					%s
				});
			';
			
			$this->view->headScript()
				->appendScript(sprintf($scripts, $this->_globalScript, $this->_readyScript));
		}
	}
}