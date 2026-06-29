<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** NewPassword Form */
class NewPasswordForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);

        $table = new CHtmlTable(array('cellpadding'=>'10', 'width'=>'320', 'class'=>'form'));
        $this->add_inner(CHtmlElement::wrap($table, 'center'));
        
        $table->add_row('Enter new password:', array('align'=>'center', 'style'=>'font-size:16px;font-weight:bold;'));
        
        $ctrl = new CHtmlPassword('password1', null, array('style'=>'width:250px;'));
        $this->setup_hint($ctrl, 'New password');
        $table->add_row($ctrl, array('align'=>'center'));
        
        $ctrl = new CHtmlPassword('password2', null, array('style'=>'width:250px;'));
        $this->setup_hint($ctrl, 'Confirm new password', true);
        $table->add_row($ctrl, array('align'=>'center'));

        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $table->add_row($ctrl, array('style'=>'padding-bottom:20px;', 'align'=>'center'));
    }
}
?>