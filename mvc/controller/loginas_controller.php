<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Login As Controller */
class LoginAsController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $login = $this->get_value('login');
        $password = $this->get_value('password');
        $access = $this->get_value('access');
        
        LoggableAccess::Instance()->log_as(true);
        
        if (!LoggableAccess::Instance()->login($login, $password, $access))
        {
            CHtmlPage::set_last_error(LoggableAccess::Instance()->get_last_error());
            return false;
        }
        
        return true;
    }
}
?>