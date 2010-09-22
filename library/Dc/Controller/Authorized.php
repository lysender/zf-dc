<?php

class Dc_Controller_Authorized extends Dc_Controller_Template
{
	public function init()
	{
		if ( ! Dc_Auth::getInstance()->isValid())
		{
			$this->_redirect('/login');
		}
		
		parent::init();
	}
}