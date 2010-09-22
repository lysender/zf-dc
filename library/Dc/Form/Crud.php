<?php

/** 
 * Basic form for crud operations
 * 
 * @author Leonel
 */
abstract class Dc_Form_Crud extends Dc_Form_Abstract
{
	/** 
	 * Model for basic data interaction
	 * 
	 * @var Dc_Model_Abstract
	 */
	protected $_model;
	
	/** 
	 * Sets the model used for validation and data interaction
	 * 
	 * @param Dc_Model_Crud $model
	 * @return $this
	 */
	public function setModel(Dc_Model_Crud $model)
	{
		$this->_model = $model;
		
		return $this;
	}
	
	/** 
	 * Returns the model object
	 * 
	 * @return Dc_Model_Crud
	 */
	public function getModel()
	{
		return $this->_model;
	}
}