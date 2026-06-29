<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Logoff Controller */
class LogoffController extends CController
{
    /** Initialize the controller */
	protected function initialize()
	{
	    $operator = LoggableAccess::Instance()->get_logged();
	    
	    if ($operator)
	    {
    	    $name = $operator->get_name().' '.$operator->get_last_name();
    	    $this->set_arg('name', $name);
	    }
	    
		$this->enable_post(true);
	}

	/** Process the form */
	protected function process()
	{
	    CHtmlForm::set_value(SELECTOR_LEDGER_ID, null, true);
	    return LoggableAccess::Instance()->logoff();
	}
}
?>