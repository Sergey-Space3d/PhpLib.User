<?php 
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Login Controller */
class LoginController extends CController
{
    /** Initialize the controller */
	protected function initialize()
	{
		$this->enable_post(true);
	}
	
	/** Process the form */
	protected function process()
	{
		$login = $this->get_value('login');
		$password = $this->get_value('password');
		
		LoggableAccess::Instance()->log_as(false);
		
		if (!LoggableAccess::Instance()->login($login, $password))
		{
		    CHtmlPage::set_last_error(LoggableAccess::Instance()->get_last_error());
		    return false;
		}
		
		return true;
	}
}
?>