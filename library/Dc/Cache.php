<?php

/** 
 * Simple caching 
 * 
 * @author lysender
 */
class Dc_Cache
{
	/** 
	 * @var Zend_Cache
	 */
	protected $_cache;
	
	/** 
	 * @var Dc_Cache
	 */
	protected static $_instance;
	
	/** 
	 * Returns a singleton instance
	 * 
	 * @return $this
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
	 * Initialize cache from registry
	 * 
	 * @return void
	 */
	protected function __construct()
	{
		$this->_cache = Zend_Registry::get('cache');
	}
	
	/** 
	 * Returns the cache object
	 * 
	 * @return Zend_Cache
	 */
	public function getCache()
	{
		return $this->_cache;
	}
	
	/** 
	 * Sets the cache object
	 * 
	 * @param Zend_Cache $cache
	 * @return $this
	 */
	public function setCache(Zend_Cache $cache)
	{
		$this->_cache = $cache;
		
		return $this;
	}
	
	/** 
	 * Sets a cache data
	 * 
	 * @param string $cacheId
	 * @param mixed $data
	 * @return $this
	 */
	public function set($cacheId, $data)
	{
		$this->_cache->save($data, $cacheId);
		
		return $this;
	}
	
	/** 
	 * Returns the cached data
	 * 
	 * @param string $cacheId
	 * @return mixed
	 */
	public function get($cacheId)
	{
		return $this->_cache->load($cacheId);
	}
	
	/** 
	 * Removes a cache entry
	 * 
	 * @param string $cacheId
	 * @return $this
	 */
	public function remove($cacheId)
	{
		$this->_cache->remove($cacheId);
		
		return $this;
	}
	
	/** 
	 * Clears the cache
	 * 
	 * @return $this
	 */
	public function clear()
	{
		$this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
		
		return $this;
	}
}