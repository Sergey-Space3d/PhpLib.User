<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Operator Form */
class DeleteOperatorForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $label = Operator::label()->lower();
        $this->set_confirm("Confirm removing the {$label}");
        
        $this->add_inner(new CHtmlHidden('id', $id));
        
        $ctrl = new CHtmlSubmit('Remove', array('class'=>($class ? $class : 'deck')));
        $ctrl->disable_on_click();
        $this->add_inner($ctrl);
    }
}
?>