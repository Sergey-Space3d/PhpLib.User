<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Owner Controller */
class BlockOwnerController extends CController
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
        $block = $this->get_value('block');
        
        $owner = new Owner($id_owner);
        
        $name = $owner->get_name();
        $label = Owner::label();
        $action = $block ? 'block' : 'unblock';
        
        $success = ServerAdmin::Instance()->block_owner($id_owner, $block);
        
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