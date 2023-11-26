<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** New Password Controller */
class NewPasswordController extends CController
{
    /** Initialize the controller */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $contact = unserialize($_SESSION['recovery_user']);
        
        if (!($contact instanceof Contact))
        {
            CHtmlPage::set_last_error('Input error: re-enter password');
            CHtmlMenu::set_cur_page('EnterVerificationCode');
            return false;
        }
        
        $password1 = $this->get_value('password1');
        $password2 = $this->get_value('password2');
        
        $validator = new UserFieldValidator();
        
        if (!$validator->is_password($password1, $password2, 'Password'))
        {
            CHtmlPage::set_last_error($validator->get_error('Password'));
            return false;
        }
        
        // Save password
        $contact->set_password($password1, true);
        
        unset($_SESSION['verification_code']);
        unset($_SESSION['recovery_user']);

        if (LoggableAccess::Instance()->login($contact->get_login(), $contact->get_password()))
        {
            LoggableAccess::Instance()->get_logged()->set_flag(Contact::CHANGE_PASSWORD, false, true);
            CHtmlPage::set_last_info('Password Updated');
            return true;
        }
        
        $name = "{$contact->get_name()} {$contact->get_last_name()}";
        CHtmlPage::set_last_error("Login failed for {$name}");
        return false;
    }
}
?>