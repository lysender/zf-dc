<?php

abstract class Dc_Mapper_Db
{
	/** 
	 * Database abstraction layer object
	 * 
	 * @var Zend_Db
	 */
	protected $_db;
	
	/**
	 * Database table name
	 * 
	 * @var string
	 */
	protected $_table;
	
	/** 
	 * Returns the table name
	 * 
	 * @return string
	 */
	public function getTable()
	{
		return $this->_table;
	}
	
	/** 
	 * Sets the table name for this data mapper
	 * 
	 * @param string $table
	 * @return $this
	 */
	public function setTable($table)
	{
		$this->_table = $table;
		
		return $this;
	}
	
	/** 
	 * Returns the database adapter object
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDb()
	{
		if ($this->_db === null)
		{
			$this->_db = Zend_Db_Table::getDefaultAdapter();
		}
		
		return $this->_db;
	}
	
	/** 
	 * Sets the database adapter object
	 * 
	 * @param Zend_Db_Adapter_Abstract $db
	 * @return $this
	 */
	public function setDb(Zend_Db_Adapter_Abstract $db)
	{
		$this->_db = $db;
		
		return $this->_db;
	}
}