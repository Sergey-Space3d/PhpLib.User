<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** PasswordRecovery Form */
class PasswordRecoveryForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $this->add_inner(new CHtmlHidden('login', $login));
        
        $table = new CHtmlTable(array('cellpadding'=>'10', 'width'=>'320', 'class'=>'form'));
        $this->add_inner(CHtmlElement::wrap($table, 'center'));
        
        $table->add_row('Recover your account', array('align'=>'center', 'style'=>'font-weight:bold;font-size:22px;'));
        $table->add_row('Enter registered email address,<br/>or phone number:', array('align'=>'center'));
        
        $ctrl = new CHtmlText('email_or_phone', $email_or_phone, array('style'=>'width:250px;'));
        $this->setup_hint($ctrl, 'Email address or phone number', true);
        $table->add_row($ctrl, array('align'=>'center'));
        
        $table->add_row('We\'ll send you verification code', array('align'=>'center'));
        
        $ctrl = new CHtmlSubmit('Continue');
        $ctrl->disable_on_click();
        $table->add_row($ctrl, array('style'=>'padding-bottom:20px;', 'align'=>'center'));
    }
}
?>