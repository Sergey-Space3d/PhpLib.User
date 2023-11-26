<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Operator Group Form */
class DeleteOperatorGroupForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $label = Operator::label()->lower();
        $this->set_confirm("Confirm removing the {$label} group");
        
        $this->add_inner(new CHtmlHidden('id', $id));
        
        $ctrl = new CHtmlSubmit('Remove', array('class'=>($class ? $class : 'deck')));
        $ctrl->disable_on_click();
        $this->add_inner($ctrl);
    }
}
?>