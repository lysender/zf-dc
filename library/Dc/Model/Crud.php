<?php

/** 
 * Supports basic CRUD - create, read, update and delete
 * 
 * @author Leonel
 *
 */
abstract class Dc_Model_Crud extends Dc_Model_Abstract
{
	/** 
	 * Begins a database transaction
	 * 
	 * @return $this
	 */
	public function beginTransaction()
	{
		$this->getMapper()->getDb()->beginTransaction();

		return $this;
	}
	
	/** 
	 * Commits a database transaction
	 * 
	 * @return $this
	 */
	public function commitTransaction()
	{
		$this->getMapper()->getDb()->commit();
		
		return $this;
	}
	
	/** 
	 * Rollback the database transaction
	 * 
	 * @return $this
	 */
	public function rollbackTransaction()
	{
		$this->getMapper()->getDb()->rollBack();
		
		return $this;
	}
	
	/** 
	 * Creates a new record to the data source
	 * 
	 * @param array $data
	 * @return int
	 */
	public function create(array $data)
	{
		return $this->getMapper()->insert($data);
	}
	
	/** 
	 * Updates a record or records
	 * 
	 * @param mixed $id
	 * @param array $data
	 * @return int
	 */
	public function update($id, array $data)
	{
		return $this->getMapper()->update($id, $data);
	}
	
	/** 
	 * Deletes a record
	 * 
	 * @param mixed $id
	 * @return int
	 */
	public function delete($id)
	{
		return $this->getMapper()->delete($id);
	}
	
	/** 
	 * Returns a single record
	 * 
	 * @param mixed $id
	 * @return array
	 */
	public function get($id)
	{
		return $this->getMapper()->get($id);
	}
	
	/** 
	 * Returns all records
	 * 
	 * @param mixed $filter
	 * @return array
	 */
	public function getAll($filter = null)
	{
		return $this->getMapper()->getAll($filter);
	}
	
	/** 
	 * Returns true if the id (primary key) exists
	 * 
	 * @param mixed $id
	 * @return boolean
	 */
	public function idExists($id)
	{
		return $this->getMapper()->idExists($id);
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
		return $this->getMapper()->nameExists($name, $excludeId);
	}
	
	/** 
	 * Returns true if name is indeed unique
	 * If exludeId is given, it excludes the id from search
	 * 
	 * @param string $name
	 * @param mixed $excludeId
	 */
	public function nameUnique($name, $excludeId = null)
	{
		return ! $this->nameExists($name, $excludeId);
	}
	
	/** 
	 * Returns true if an id has dependency on related models
	 * 
	 * @param mixed $id
	 * @return boolean
	 */
	public function hasDependency($id)
	{
		// No dependency by default
		return false;
	}
}