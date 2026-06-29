<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Access Controller */
class EditOperatorAccessController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $operator = new Operator($id);
        
        $name = "{$operator->get_name()} {$operator->get_last_name()}";
        $this->set_arg('name', $name);
        
        static $access_links = null;
        static $access_nolinks = null;
        
        if ($access_links === null) 
        {
            $operator_id = LoggableAccess::Instance()->get_logged()->get_id();
            $is_admin = LoggableAccess::Instance()->is_logged_admin();
            $available_access = AccessMan::Instance()->get_access($operator_id, $is_admin, true);
            
            $access_links = AccessMan::Instance()->filter_links($available_access, true, false);
            $access_nolinks = AccessMan::Instance()->filter_links($available_access, false, false);
        }
        
        $operator_access = $this->is_submitted() ? CHtmlForm::get_value('operator_access') :
        array_keys(AccessMan::Instance()->get_access($id, $operator->is_admin(), false));
        
        $this->set_arg('access_links', $access_links);
        $this->set_arg('access_nolinks', $access_nolinks);
        $this->set_arg('operator_access', $operator_access);
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = $this->get_value('id');
        $operator_access = $this->get_value('operator_access');
        
        if (!$operator_access)
        {
            CHtmlPage::set_last_error('Please select at least one access property');
            return false;
        }
        
        // Get available access ids
        $access = LoggableAccess::Instance();
        $available_access = AccessMan::Instance()->get_access($access->get_logged()->get_id(), $access->is_logged_admin(), false);
        $available_access = array_keys($available_access);
        
        // Get access ids not present in operator access array
        $access_to_remove = array_diff($available_access, $operator_access);
        
        // Update operator access
        AccessMan::Instance()->add_access($id, $operator_access);
        AccessMan::Instance()->remove_access($id, $access_to_remove);
        
        // This will force request update
        $operator = new Operator($id);
        $operator->touch();
        
        CHtmlPage::set_last_info("Access was updated for {$operator->get_name()} {$operator->get_last_name()}");
        
        return true;
    }
}
?>