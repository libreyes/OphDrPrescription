<?php

class OphTrPrescribingModule extends BaseEventTypeModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'OphTrPrescribing.models.*',
			'OphTrPrescribing.views.*',
			'OphTrPrescribing.components.*',
			'OphTrPrescribing.controllers.*',
		));
		parent::init();
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
