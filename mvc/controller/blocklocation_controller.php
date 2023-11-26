<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Location Controller */
class BlockLocationController extends CController
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
        $block = $this->get_value('block');
        
        $location = new Location($id_location);
        
        $name = $location->get_name();
        $label = Location::label();
        $action = $block ? 'block' : 'unblock';
        
        $success = ServerAdmin::Instance()->block_location($id_location, $block);
        
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