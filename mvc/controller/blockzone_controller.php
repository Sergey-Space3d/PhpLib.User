<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Zone Controller */
class BlockZoneController extends CController
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
        $block = $this->get_value('block');
        
        $zone = new Zone($id_zone);
        
        $name = $zone->get_name();
        $label = Zone::label();
        $action = $block ? 'block' : 'unblock';
        
        $success = ServerAdmin::Instance()->block_zone($id_zone, $block);
        
        if ($success)
        {
            CHtmlPage::set_last_info("{$label} {$name} was {$action}ed");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to {$action} {$label} {$name}");
        return false;
    }
}
?>