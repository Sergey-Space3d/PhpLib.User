<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** EnterVerificationCode Controller */
class EnterVerificationCodeController extends CController
{
    /** Initialize the controller */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $verification_code = $this->get_value('code');
        
        if ($verification_code == $_SESSION['verification_code'])
        {
            // Go to the next form
            CHtmlMenu::set_cur_page('NewPassword');
            return true;
        }
        
        $error = $verification_code ? "The verification code '{$verification_code}' is not valid" :
        "Enter the verification code";
        CHtmlPage::set_last_error($error);

        return false;
    }
}
?>