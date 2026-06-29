<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Owner Controller */
class DeleteOwnerController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id_owner = (int)$this->get_value('id');
        $owner = new Owner($id_owner);
        
        $name = $owner->get_name();
        $label = Owner::label();
        
        $success = ServerAdmin::Instance()->delete_owner($id_owner);
        
        if ($success)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_OWNER_ID, null, true);
            CHtmlForm::set_value(SELECTOR_LOCATION_ID, null, true);
            CHtmlForm::set_value(SELECTOR_ZONE_ID, null, true);
            
            CHtmlPage::set_last_info("{$label} {$name} was deleted");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to delete {$label} {$name}");
        return false;
    }
}
?>