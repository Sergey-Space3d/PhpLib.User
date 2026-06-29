<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Operator Form */
class BlockOperatorForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $action = $block ? 'blocking' : 'unblocking';
        $label = Operator::label()->lower();
        $this->set_confirm("Confirm {$action} the {$label}");
        
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('block', $block));
        
        $ctrl = new CHtmlSubmit($block ? 'Block' : 'Unblock', array('class'=>($class ? $class : 'deck')));
        $ctrl->disable_on_click();
        $this->add_inner($ctrl);
    }
}
?>