<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Zone Form */
class EditZoneForm extends FormLayout
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        $is_new = $id == 0;
        
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('id_owner', $id_owner));

        $label = Zone::label();
        $title = $is_new ? "Add {$label}" : "Edit {$label}";
        $this->set_title('<b>'.$title.'</b>');
        
        if ($owner_name)
        {
            $this->add_control(Owner::label(), $owner_name);
            $this->add_hr();
        }
        
        $this->add_control('Name', new CHtmlText('name', $name), true, true);
        
        if ($use_external_id) 
        {
            $this->add_control("{$label} Id", new CHtmlText('external_id', $external_id), true, true);
            $this->add_inner(new CHtmlHidden('use_external_id', true));
        }
        else
        {
            $this->add_inner(new CHtmlHidden('external_id', $external_id));
        }
        
        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $this->add_control_deck($ctrl);
    }
}
?>