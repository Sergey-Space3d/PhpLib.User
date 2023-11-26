<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Owner Form */
class EditOwnerForm extends FormLayout
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $is_new = $id == 0;
        $this->add_inner(new CHtmlHidden('id', $id));
        
        $label = Owner::label();
        $title = $is_new ? 'Add' : 'Edit';
        $title = "{$title} {$label}";
        $this->set_title('<b>'.$title.'</b>');
        
        $this->add_control('Name', new CHtmlText('name', $name), true, true);
        $this->add_control('Email', new CHtmlText('email', $email), true, true);
        $this->add_control('Phone', new CHtmlText('phone', $phone), true, false);
        
        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $this->add_control_deck($ctrl);
    }
}
?>