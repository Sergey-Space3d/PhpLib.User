<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** EnterVerificationCode Form */
class EnterVerificationCodeForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);

        $table = new CHtmlTable(array('cellpadding'=>'10', 'width'=>'320', 'class'=>'form'));
        $this->add_inner(CHtmlElement::wrap($table, 'center'));
        
        $table->add_row('Enter the verification code:', array('align'=>'center', 'style'=>'font-size:16px;font-weight:bold;'));
        
        $style = 'width:250px;margin:0px 10px;text-align:center;font-size:20px;letter-spacing:5px;';
        $ctrl = new CHtmlText('code', null, array('maxlength'=>6, 'style'=>$style));
        $this->setup_hint($ctrl, '------', true);
        $table->add_row($ctrl);
        
        $ctrl = new CHtmlSubmit('Continue');
        $ctrl->disable_on_click();
        $table->add_row($ctrl, array('style'=>'padding-bottom:20px;', 'align'=>'center'));
    }
}
?>