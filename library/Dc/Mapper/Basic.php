<?php

abstract class Dc_Mapper_Basic extends Dc_Mapper_Db
{	
	/** 
	 * Primary key for the table
	 * 
	 * When single primary key, it is a string
	 * When multiple primary key, it is a numeric array
	 * 
	 * ex: array('code', 'seq')
	 * 
	 * @var mixed
	 */
	protected $_primaryKey = 'id';
	
	/** 
	 * Name key used to name the whole record
	 * 
	 * @var string
	 */
	protected $_nameKey = 'name';
	
	/** 
	 * Default sort order
	 * Either a string for the default sort order
	 * or an array of sort order
	 * 
	 * @var mixed
	 */
	protected $_defaultOrder;
	
	/** 
	 * Returns true if the id exists
	 * 
	 * @param mixed $id
	 * @return boolean
	 */
	public function idExists($id)
	{
		if (is_array($this->_primaryKey))
		{
			throw new Dc_Mapper_Exception('idExists currently does not work with composite primary key');
		}
		
		return $this->fieldValueExists($this->_primaryKey, $id, null);
	}
	
	/**
	 * Returns true if the name exists
	 *
	 * @param string $name
	 * @param mixed $excludeId
	 * @return boolean
	 */
	public function nameExists($name, $excludeId = null)
	{
		if (is_array($this->_primaryKey))
		{
			throw new Dc_Mapper_Exception('nameExists currently does not work with composite primary key');
		}
		
		return $this->fieldValueExists(
			$this->_nameKey,
			$name,
			($excludeId) ? $excludeId : null
		);
	}

	/**
	 * Returns true if a certain field value exists
	 * If excludeId is present, it excludes from search
	 *
	 * @param string $field
	 * @param string $value
	 * @param mixed $excludeId
	 * @return boolean
	 */
	public function fieldValueExists($field, $value, $excludeId = null)
	{
		if (is_array($this->_primaryKey))
		{
			throw new Dc_Mapper_Exception('fieldValueExists currently does not work with composite primary key');
		}
		
		$db = $this->getDb();
		
		$select = $db->select()
			->from($this->_table)
			->where("$field = ?", $value);
			
		if ($excludeId !== null)
		{
			$select->where("$this->_primaryKey <> ?", $excludeId);
		}
		
		return (boolean) $db->fetchRow($select);
	}
	
	/**
	 * Adds a new record
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function insert(array $data)
	{
		return $this->getDb()
			->insert($this->_table, $data);
	}
	
	/**
	 * Updates a record or records
	 * 
	 * @param mixed $id
	 * @param array $data
	 * @return boolean
	 */
	public function update($id, array $data)
	{		
		$db = $this->getDb();
		
		$where = array();
		
		if (is_array($this->_primaryKey))
		{
			foreach ($this->_primaryKey as $field)
			{
				if (isset($id[$field]))
				{
					$where[] = $db->quoteInto($field.' = ?', $id[$field]);
				}
			}
			
			if (count($where) != count($this->_primaryKey))
			{
				throw new Dc_Mapper_Exception('Composite primary key is does not match with parameters');
			}
		}
		else 
		{
			$where = $db->quoteInto($this->_primaryKey.' = ?', $id);
		}
		
		return $db->update($this->_table, $data, $where);
	}
	
	/**
	 * Deletes a record or records
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public function delete($id)
	{		
		$db = $this->getDb();
		
		$where = array();
		
		if (is_array($this->_primaryKey))
		{
			foreach ($this->_primaryKey as $field)
			{
				if (isset($id[$field]))
				{
					$where[] = $db->quoteInto($field.' = ?', $id[$field]);
				}
			}
			
			if (count($where) != count($this->_primaryKey))
			{
				throw new Dc_Mapper_Exception('Composite primary key is does not match with parameters');
			}
		}
		else 
		{
			$where = $db->quoteInto($this->_primaryKey.' = ?', $id);
		}
		
		return $db->delete($this->_table, $where);
	}
	
	/**
	 * Retrieves a record
	 * 
	 * @param mixed $id
	 * @return array
	 */
	public function get($id)
	{
		$db = $this->getDb();
		
		$select = $db->select()
			->from($this->_table);
			
		$where = array();
		
		if (is_array($this->_primaryKey))
		{
			foreach ($this->_primaryKey as $field)
			{
				if (isset($id[$field]))
				{
					$select->where($field.' = ?', $id[$field]);
				}
			}
			
			// TODO: 
			// Instead of matching id and primary key,
			// it is better to match the total where (for future)
			 
			if (count($id) != count($this->_primaryKey))
			{
				throw new Dc_Mapper_Exception('Composite primary key is does not match with parameters');
			}
		}
		else 
		{
			$select->where($this->_primaryKey.' = ?', $id);
		}
			
		return $db->fetchRow($select);
	}
	
	/**
	 * Returns all records - whether we like it or not
	 * When filter is specified, it filters the search
	 * through the field => value parameters on filters
	 * 
	 * Each field in filter becomes inequality operator
	 * in where clause
	 * 
	 * @param mixed $filter
	 * @return array
	 */
	public function getAll($filter = null)
	{
		$db = $this->getDb();
		$select = $db->select()
			->from($this->_table);
		
		if (is_array($filter))
		{
			foreach ($filter as $field => $value)
			{
				$select->where($field.' <> ?', $value);
			}
		}
			
		if ($this->_defaultOrder)
		{
			$select->order($this->_defaultOrder);
		}
		
		return $db->fetchAll($select);
	}
	
	/** 
	 * Searches the database
	 * 
	 * @param array $params
	 */
	public function find(array $params, array $filter = array())
	{
		$db = $this->getDb();
		$select = $db->select()
			->from($this->_table);
		
		if ( ! empty($params))
		{
			foreach ($params as $field => $value)
			{
				$select->where($field.' = ?', $value);
			}
		}
		
		if ( ! empty($filters))
		{
			foreach ($filters as $field => $value)
			{
				$select->where($field.' <> ?', $value);
			}
		}
			
		if ($this->_defaultOrder)
		{
			$select->order($this->_defaultOrder);
		}
		
		return $db->fetchAll($select);
	}
}