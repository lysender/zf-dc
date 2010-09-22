<?php

/**
 * Authentication class
 * 
 * @author lysender
 */
class Dc_Auth
{	
	/**
	 * Session namespace used to identify current
	 * user's session variables
	 * 
	 * @var string
	 */
	const NAMESPACE = 'CURRENT_USER';
	
	/**
	 * @var Zend_Auth
	 */
	public $auth;
	
	/**
	 * @var Zend_Session
	 */
	public $session;
	
	/**
	 * Static instance for singleton pattern
	 *
	 * @var Dc_Auth
	 */
	protected static $_instance;
	
	/**
	 * Session keys
	 *
	 * @var array
	 */
	protected $_keys = array(
		'username',
		'ipAddress',
		'userAgent',
		'timestamp'
	);
	
	/**
	 * Current data
	 *
	 * @var array
	 */
	protected $_current = array();
	
	/**
	 * Previous data
	 *
	 * @var array
	 */
	protected $_previous = array();
	
	/**
	 * @var Default_Model_User
	 */
	protected $_user;
	
	/**
	 * __construct()
	 *
	 * @return void
	 */
	protected function __construct()
	{
		// singleton pattern
		$this->auth = Zend_Auth::getInstance();
		$this->session = new Zend_Session_Namespace(self::NAMESPACE);
		
		// load previous data from session
		foreach ($this->_keys as $key)
		{
			$this->_previous[$key] = $this->session->$key;
		}
		
		// load current data
		$this->_loadCurrent();
	}
	
	/**
	 * Returns the singleton instance
	 *
	 * @return Dc_Auth
	 */
	public static function getInstance()
	{
		if (self::$_instance === null)
		{
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}
	
	/**
	 * Loads the current data from various sources
	 *
	 * @return $this
	 */
	protected function _loadCurrent()
	{
		// load username
		$current = array();
		$current['username'] = null;
		if ($this->auth->hasIdentity())
		{
			$current['username'] = $this->auth->getIdentity();
		}
		
		// load ip address
		$current['ipAddress'] = $this->getIpAddress();
		$current['userAgent'] = $this->getUserAgent();
		$current['timestamp'] = time();
		
		// save to session
		foreach ($this->_keys as $key)
		{
			$this->_current[$key] = $current[$key];
			$this->session->$key = $current[$key];
		}
	}
	
	/**
	 * Returns the previous values from a session
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getPrevious($key)
	{
		if (in_array($key, $this->_keys))
		{
			return $this->_previous[$key];
		}
		
		return null;
	}
	
	/**
	 * Returns the current value from user session
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getCurrent($key)
	{
		if (in_array($key, $this->_keys))
		{
			return $this->_current[$key];
		}
		
		return null;
	}
	
	/**
	 * Validates the current session via Zend_Auth and validating
	 * it on the database if it exists. Further checking are 
	 * recording previous ip address, user agent, and previous timestamp
	 * to let expirations on idle sessions
	 * 
	 * @return true|false
	 */
	public function isValid()
	{
		$username = $this->getCurrent('username');
		
		if ( ! $username)
		{
			return false;
		}
		
		// Check if user is a valid system user
		if ( ! $this->validUser($username))
		{
			return false;
		}
		
		// Check if ip address matches
		if ($this->getPrevious('ipAddress') != $this->getCurrent('ipAddress'))
		{
			return false;
		}
		
		// Check if the user agents matches
		if ($this->getPrevious('userAgent') != $this->getCurrent('userAgent'))
		{
			return false;
		}
		
		// Check idle time
		$idleTime = $this->getCurrent('timestamp') - $this->getPrevious('timestamp');
		
		return true;
	}
	
	/**
	 * Returns true if the user is a valid system user
	 *
	 * @return boolean
	 */
	public function validUser($username)
	{
		$user = new Default_Model_User;
		return $user->userExists($username);
	}
	
	/**
	 * Gets the client's ip address from header
	 * 
	 * @return string
	 */
	public function getIpAddress()
	{
		$ip = null;
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	    return trim($ip);
	}
	
	/**
	 * Gets the user agent from the current HTTP header
	 * 
	 * @return String
	 */
	public function getUserAgent()
	{
		return trim($_SERVER['HTTP_USER_AGENT']);
	}
}