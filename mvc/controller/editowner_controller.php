<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Owner Controller*/
class EditOwnerController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $owner = new Owner($id);
        
        $this->set_arg('name', $this->choose_value($owner->get_name(), 'name'));
        $this->set_arg('email', $this->choose_value($owner->get_email(), 'email'));
        $this->set_arg('phone', $this->choose_value($owner->get_phone(), 'phone'));
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = $this->get_value('id');
        $name = $this->get_value('name');
        $email = $this->get_value('email');
        $phone = $this->get_value('phone');
        
        $label = Owner::label();
        $validator = new UserFieldValidator();
        
        if (!$validator->is_name($name, 'Name'))
        {
            CHtmlForm::set_error($validator->get_error('Name'), 'name');
        }
        else if (OwnerMan::Instance()->is_name_reserved($name, $id))
        {
            CHtmlForm::set_error("Name {$name} is already used", 'name');
        }
        
        if (!$validator->is_email($email, 'Email'))
        {
            CHtmlForm::set_error($validator->get_error('Email'), 'email');
        }
        
        if ($phone && !$validator->is_phone($phone, 'Phone number'))
        {
            CHtmlForm::set_error($validator->get_error('Phone number'), 'phone');
        }
        
        $errors = CHtmlForm::get_errors();
        
        if ($errors)
        {
            CHtmlPage::set_last_error(implode('<br/>', $errors));
            return false;
        }
        
        $owner = new Owner($id);
        $owner->set_name($name);
        $owner->set_email($email);
        $owner->set_phone($phone);
        
        try
        {
            if (!ServerAdmin::Instance()->edit_owner($owner))
            {
                $action = $id ? 'update' : 'add';
                CHtmlPage::set_last_error("Failed to {$action} {$label} {$name}");
                return false;
            }
        }
        catch (Exception $e)
        {
            CHtmlPage::set_last_error($e->getMessage());
            return false;
        }
        
        if ($id == 0)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_OWNER_ID, $owner->get_id(), true);
            CHtmlForm::set_value(SELECTOR_LOCATION_ID, null, true);
            CHtmlForm::set_value(SELECTOR_ZONE_ID, null, true);
        }
        
        $action = $id ? 'updated' : 'added';
        CHtmlPage::set_last_info("{$label} {$name} was {$action}");
        
        return true;
    }
}
?>