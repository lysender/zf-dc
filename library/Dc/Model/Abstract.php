<?php

/** 
 * Basic modeling
 * 
 * Dependency:
 * 
 * Mapper - shoud be set at init, when it must
 * 		be loaded automatically, otherwise load it separately
 * 		via setMapper()
 * 
 * @author Leonel
 */
abstract class Dc_Model_Abstract
{
	/** 
	 * Data mapper object
	 * 
	 * @var Dc_Mapper_Db
	 */
	protected $_mapper;
	
	/**
	 * @var Dc_Cache
	 */
	protected $_cache;
	
	/** 
	 * Cache prefix used to name cache ids
	 * 
	 * @var string
	 */
	protected $_cachePrefix;
	
	/** 
	 * Compatible models that can check dependency for a record
	 * 
	 * @var array
	 */
	protected $_dependencyModels = array();
	
	/** 
	 * Returns the cache object
	 * 
	 * @return Dc_Cache
	 */
	public function getCache()
	{
		if ($this->_cache === null)
		{
			$this->_cache = Dc_Cache::getInstance();
		}
		
		return $this->_cache;
	}
	
	/** 
	 * Sets the caching obhect
	 * 
	 * @param Dc_Cache $cache
	 * @return $this
	 */
	public function setCache(Dc_Cache $cache)
	{
		$this->_cache = $cache;
		
		return $this;
	}
	
	/** 
	 * Sets the mapper object
	 * 
	 * @param Dc_Mapper_Db $mapper
	 */
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		
		return $this;
	}
	
	/** 
	 * Returns the mapper object
	 * 
	 * @return Dc_Mapper_Db
	 */
	public function getMapper()
	{
		return $this->_mapper;
	}
	
	/** 
	 * Dependency checking - usually used before deleting
	 * or updating a record that may affect dependent records
	 * 
	 * No dependency by default
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public function hasDependency($id)
	{
		return false;
	}
	
	/** 
	 * Sets the dependency models
	 * 
	 * @param array $mappers
	 * @return $this
	 */
	public function setDependencyModels(array $models)
	{
		$this->_dependencyModels = $models;
		
		return $this;
	}
	
	/** 
	 * Returns all dependency model objects as an array
	 * 
	 * @return array
	 */
	public function getDependencyModels()
	{
		return $this->_dependencyModels;
	}
}