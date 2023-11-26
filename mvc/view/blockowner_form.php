<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Owner Form */
class BlockOwnerForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $action = $block ? 'blocking' : 'unblocking';
        $label = Owner::label()->lower();
        $this->set_confirm("Confirm {$action} the {$label}");
        
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('block', $block));
        
        $ctrl = new CHtmlSubmit($block ? 'Block' : 'Unblock', array('class'=>($class ? $class : 'deck')));
        $ctrl->disable_on_click();
        $this->add_inner($ctrl);
    }
}
?>