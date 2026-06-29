<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Delete Zone Controller */
class DeleteZoneController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id_zone = (int)$this->get_value('id');
        $zone = new Zone($id_zone);
        
        $label = Zone::label();
        $name = $zone->get_name();
        
        $success = ServerAdmin::Instance()->delete_zone($id_zone);
        
        if ($success)
        {
            // Reset the selector
            CHtmlForm::set_value(SELECTOR_ZONE_ID, null, true);
            
            CHtmlPage::set_last_info("{$label} '{$name}' was deleted");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to delete {$label} '{$name}'");
        return false;
    }
}
?>