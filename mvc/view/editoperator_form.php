<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Form */
class EditOperatorForm extends FormLayout
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        $is_new = $id == 0;
        
        $this->add_inner(new CHtmlHidden('id', $id));
        $this->add_inner(new CHtmlHidden('id_owner', $id_owner));
        $this->add_inner(new CHtmlHidden('id_zone', $id_zone));
        $this->add_inner(new CHtmlHidden('id_access', $id_access));
        
        $label = Operator::label();
        $title = $is_new ? 'Add' : 'Edit';
        $title = "{$title} {$label}";
        $this->set_title('<b>'.$title.'</b>');
        
        if ($owner_name)
        {
            $this->add_control(Owner::label(), $owner_name);
            $this->add_hr();
        }
        
        if ($levels)
        {
            $ctrl_level = new CHtmlCbox('level', $level);
            $this->add_control('Admin Level', $ctrl_level);
            $ctrl_level->add_items($levels);
            
            $this->add_hr();
        }
        
        $this->add_control('First Name', new CHtmlText('first_name', $first_name), true, true);
        $this->add_control('Last Name', new CHtmlText('last_name', $last_name), true, true);
        $this->add_control('Email', new CHtmlText('email', $email), true, true);
        $this->add_control('Phone', new CHtmlText('phone', $phone), true, false);
        
        $ctrl_tz = new CHtmlCbox('timezone', $timezone);
        $ctrl_tz->add_items($timezones);
        $this->add_control('Time Zone', $ctrl_tz);
        
        $ctrl = new CHtmlSubmit('Submit');
        $ctrl->disable_on_click();
        $this->add_control_deck($ctrl);
    }
}
?>