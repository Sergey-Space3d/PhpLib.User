<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Block Operator Controller */
class BlockOperatorController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id_operator = (int)$this->get_value('id');
        $block = $this->get_value('block');
        
        $operator = new Operator($id_operator);
        
        $name = "{$operator->get_name()} {$operator->get_last_name()}";
        $label = Operator::label();
        $action = $block ? 'block' : 'unblock';
        
        $success = ServerAdmin::Instance()->block_operator($id_operator, $block);
        
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