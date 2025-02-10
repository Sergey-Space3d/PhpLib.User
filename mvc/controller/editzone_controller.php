<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Zone Controller */
class EditZoneController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $zone = new Zone($id);
        
        $this->set_arg('name', $this->choose_value($zone->get_name(), 'name'));
        $this->set_arg('external_id', $this->choose_value($zone->get_external_id(), 'external_id'));
        
        // Set owner fields
        $id_owner = $this->get_arg('id_owner');
        
        if ($id_owner)
        {
            $owner = new Owner($id_owner);
            $this->set_arg('owner_name', $owner->get_name()); // read-only
        }
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = $this->get_value('id');
        $id_owner = $this->get_value('id_owner');
        $name = $this->get_value('name');
        $external_id = $this->get_value('external_id');
        
        $label = Zone::label();
        $validator = new UserFieldValidator();
        
        if (!$validator->is_name($name, 'Name'))
        {
            $error = $validator->get_error('Name');
            CHtmlForm::set_error($error, 'name');
            CHtmlPage::set_last_error($error);
            return false;
        }
        
        if ($this->get_value('use_external_id') && !$external_id)
        {
            CHtmlForm::set_error('Id is invalid', 'external_id');
            CHtmlPage::set_last_error('Id is invalid');
            return false;
        }
        
        $zone = new Zone($id);
        $zone->set_owner_id($id_owner);
        $zone->set_name($name);
        $zone->set_external_id($external_id);
        
        if (!ServerAdmin::Instance()->edit_zone($zone))
        {
            $action = $id ? 'update' : 'add';
            CHtmlPage::set_last_error("Failed to {$action} {$label} '{$name}'");
            return false;
        }
        else if (!$id)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_ZONE_ID, $zone->get_id(), true);
        }
        
        $action = $id ? 'updated' : 'added';
        CHtmlPage::set_last_info("{$label} '{$name}' was {$action}");
        
        return true;
    }
}
?>