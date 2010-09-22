<?php

/** 
 * CRUD controller - used for basic crud operations
 * Used for repeatedly used task
 * 
 * @author Leonel
 *
 */
class Dc_Controller_Crud extends Dc_Controller_Authorized
{
	/** 
	 * Module wherein the crud controller belongs
	 * When in default module, empty string must be used
	 * otherwise, it must be in a form of
	 * 		/modulename
	 * 
	 * @var string
	 */
	protected $_module = '';
	
	/** 
	 * First field to focus on default
	 * 
	 * @var string
	 */
	protected $_firstFocus;
	
	/** 
	 * Name of the current data to be processed
	 * 
	 * @var string
	 */
	protected $_subjectName;
	
	/** 
	 * A slug type name to indicated the current controller
	 * 
	 * @var string
	 */
	protected $_subjectController;
	
	/** 
	 * Extra parameters that is used in crud parameter for javascript
	 * This will be appended to the javascript as a return url
	 * 
	 * @var string
	 */
	protected $_subjectExtraParams;
	
	/** 
	 * The key / id to use when usually refer to as primary key in database
	 * Used as parameter in edit mode
	 * 
	 * @var string
	 */
	protected $_subjectIdKey = 'id';
	
	/**
	 * @var Dc_Form_Abstract
	 */
	protected $_form;
	
	/**
	 * @var Dc_Model_Crud
	 */
	protected $_model;
	
	/**
	 * Adds extra view scripts and css and other stuff generic for crud
	 * 
	 * @see Controller/Dc_Controller_Template::postDispatch()
	 */
	public function postDispatch()
	{
		$this->view->headLink()->appendStylesheet($this->view->serverUrl('/css/crud.css?v='.APP_VERSION));
		$this->view->headScript()->appendFile($this->view->serverUrl('/js/crud.js?v='.APP_VERSION));
		
		if ($this->getRequest()->getActionName() == 'index')
		{
			$this->view->headScript()->appendFile($this->view->serverUrl('/js/jquery.tablesorter.min.js?v='.APP_VERSION));
		}
		
		$this->_globalScript .= '
			var crudControllerIndex = "'.$this->_subjectController.'";
			var crudModulePath = "'.$this->_module.'";
		';
		
		if ($this->_subjectExtraParams)
		{
			$this->_globalScript .= '
				var crudControllerExtraParam = "'.$this->_subjectExtraParams.'";
			';			
		}

		parent::postDispatch();
	}
	
	/** 
	 * Returns the subject controller where the crud is all about
	 * 
	 * @return string
	 */
	protected function _getSubjectController()
	{
		$extra = null;
		if ($this->_subjectExtraParams)
		{
			$extra = '/index/'.$this->_subjectExtraParams;
		}
		
		return $this->view->serverUrl($this->_module.'/'.$this->_subjectController.$extra);
	}
	
	/** 
	 * Returns the extra parameters used to append in the url
	 * for operations such as list, create, update and delete
	 * whether they are form action links or urls
	 * 
	 * @return string
	 */
	protected function _getSubjectExtraParams()
	{
		if ($this->_subjectExtraParams)
		{
			return '/'.$this->_subjectExtraParams;
		}
		return null;
	}
	
	/** 
	 * Process add 
	 * 
	 * @param Default_Model_Abstract $model
	 * @param Dc_Form $form
	 */
	public function processAdd(Dc_Model_Crud $model, Dc_Form_Crud $form)
	{
		$action = $this->getRequest()->getActionName();
		
		$form->setAction($this->view->serverUrl("$this->_module/$this->_subjectController/$action"));
		
		// Process when the form is posted
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				try {
					$model->create($form->getValues());
					$this->_session->successMessage = "New $this->_subjectName added";
					$this->_redirect($this->_getSubjectController());
				}
				catch (Exception $e)
				{
					$this->view->errorMessage = "An error occured while adding a new $this->_subjectName";
					$this->_setFirstFocusScript();
					
					if (APPLICATION_ENV == 'development')
					{
						var_dump($e->getMessage());
						var_dump($e->getTraceAsString());
					}
				}
			}
			else 
			{
				$firstError = $form->getFirstError();
				$this->_setFirstFocusScript($firstError['field']);
				
				// Head error title
				$errorTitle = 'Please verify your entries and try again.';
				
				// Append an error when token gots the error
				if ($form->getElement('token')->hasErrors())
				{
					$errorTitle = 'Session timeout, try again.';
				}
				
				$this->view->errorMessage = $errorTitle;
			}
		}
		else 
		{
			if ($this->_firstFocus)
			{
				$this->_setFirstFocusScript();
			}
		}
		
		$this->view->form = $form;
	}
	
	/** 
	 * Process the edit routine
	 * 
	 * @param Dc_Model_Abstract $model
	 * @param Dc_Form $form
	 */
	public function processEdit(Dc_Model_Abstract $model, Dc_Form_Abstract $form)
	{
		// Initialize parameters
		$request = $this->getRequest();
		
		$action = $request->getActionName();
		
		$id = $request->getParam($this->_subjectIdKey);
		if ( ! $id)
		{
			$this->_session->errorMessage = ucfirst($this->_subjectName).' not specified';
			$this->_redirect($this->_getSubjectController());
		}
		
		$data = $model->get($id);
		if (empty($data))
		{
			$this->_session->errorMessage = ucfirst($this->_subjectName).' not found';
			$this->_redirect($this->_getSubjectController());
		}
		
		$form->setAction($this->view->serverUrl("$this->_module/$this->_subjectController/$action/$this->_subjectIdKey/$id"));
		$form->populate($data);
		
		// Process when form is posted
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				try {
					$model->update($id, $form->getValues());
					$this->_session->successMessage = ucfirst($this->_subjectName).' updated';
					$this->_redirect($this->_getSubjectController());
				}
				catch (Exception $e)
				{
					$this->view->errorMessage = "An error occured while updating $this->_subjectName";
					$this->_setFirstFocusScript();
					
					if (APPLICATION_ENV == 'development')
					{
						var_dump($e->getMessage());
						var_dump($e->getTraceAsString());
					}
				}
			}
			else
			{
				$firstError = $form->getFirstError();
				$this->_setFirstFocusScript($firstError['field']);
				
				// Head error title
				$errorTitle = 'Please verify your entries and try again.';
				
				// Append an error when token gots the error
				if ($form->getElement('token')->hasErrors())
				{
					$errorTitle = 'Session timeout, try again.';
				}
				
				$this->view->errorMessage = $errorTitle;
			}
		}
		else
		{
			if ($this->_firstFocus)
			{
				$this->_setFirstFocusScript();
			}
		}
		
		$this->view->form = $form;
		$this->view->update = true;
	}
	
	/** 
	 * Delete process
	 * 
	 * @param Dc_Model_Abstract $model
	 */
	public function processDelete(Dc_Model_Abstract $model)
	{
		// Initialize parameters
		$request = $this->getRequest();
		$id = $request->getParam($this->_subjectIdKey);
		if ( ! $id)
		{
			$this->_session->errorMessage = ucfirst($this->_subjectName).' not specified';
			$this->_redirect($this->_getSubjectController());
		}
		
		if ($model->hasDependency($id))
		{
			$this->_session->errorMessage = ucfirst($this->_subjectName).' cannot be deleted because some components depends on it.';
			$this->_redirect($this->_getSubjectController());			
		}
		
		try {
			$result = $model->delete($id);
			if ( ! $result)
			{
				$this->_session->errorMessage = "No $this->_subjectName has been deleted";	
			}
			else
			{
				$this->_session->successMessage = "A $this->_subjectName has been deleted";
			}
		}
		catch (Exception $e)
		{
			$this->_session->errorMessage = "An error occured while deleting a $this->_subjectName";
			
			if (APPLICATION_ENV == 'development')
			{
				var_dump($e->getMessage());
				var_dump($e->getTraceAsString());
				
				exit;
			}
		}
			
		$this->_redirect($this->_getSubjectController());
	}
	
	public function getForm()
	{		
		return $this->_form;
	}
	
	public function setForm(Dc_Form_Abstract $form)
	{
		$this->_form = $form;
		
		return $this;
	}
	
	public function getModel()
	{
		return $this->_model;
	}
	
	public function setModel(Dc_Model_Crud $model)
	{
		$this->_model = $model;
		
		return $this;
	}	

	/** 
	 * Sets the first focus element
	 * 
	 * @param string $focus
	 */
	protected function _setFirstFocusScript($focus = null)
	{
		if ($focus === null)
		{
			$focus = $this->_firstFocus;
		}
		
		$this->_readyScript .= '$("#'.$focus.'").focus();'.PHP_EOL;
	}
}