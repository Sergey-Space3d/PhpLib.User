<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Location Controller */
class DeleteLocationController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id_location = (int)$this->get_value('id');
        $location = new Location($id_location);
        
        $name = $location->get_name();
        $label = Location::label();
        
        $success = ServerAdmin::Instance()->delete_location($id_location);
        
        if ($success)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_LOCATION_ID, null, true);
            CHtmlPage::set_last_info("{$label} {$name} was deleted");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to delete {$label} {$name}");
        return false;
    }
}
?>