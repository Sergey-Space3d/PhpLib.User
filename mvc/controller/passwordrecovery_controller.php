<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** PasswordRecovery Controller */
class PasswordRecoveryController extends CController
{
    /** Initialize the controller */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $login = $this->get_value('email_or_phone');
        $contact = LoggableAccess::Instance()->recover($login);
        
        if ($contact)
        {
            // Go to the next form
            $success = $this->process_contact($contact);
            if ($success) CHtmlMenu::set_cur_page('EnterVerificationCode');
            return $success;
        }
        
        CHtmlPage::set_last_error("'{$login}' is not valid username");
        return false;
    }
    
    /** Process db result */
    protected function process_contact(Contact $contact)
    {
        $message_helper = $this->get_value('message_helper');
        
        if (!$message_helper)
        {
            CHtmlPage::set_last_error("Message Helper is not initialized");
            return false;
        }
        
        // Remember the recovery resident
        $_SESSION['recovery_user'] = serialize($contact);
        
        // 6 digits are needed, but max value of rand() is 32767 for some platforms
        $_SESSION['verification_code'] = rand(10001, 32767) * 10 - rand(0, 9);
        
        if (!TemplateMessenger::Instance($message_helper)->send_recovery_code($contact, $_SESSION['verification_code']))
        {
            CHtmlPage::set_last_error("Failure sending message to {$contact->get_email()}");
            return false;
        }
        
        CHtmlPage::set_last_info("The verification code was sent to {$contact->get_email()}");
        return true;
    }
}
?>