<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Group Controller */
class EditOperatorGroupController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = (int)$this->get_arg('id');
        $id_owner = (int)$this->get_arg('id_owner');
        $group = OperatorGroupMan::Instance()->get_ledger($id);
        
        $this->set_arg('name', $this->choose_value($group ? $group->get_name() : null, 'name'));
        
        // Operators ----------------------------------------------------
        
        $access = LoggableAccess::Instance();
        $available_operators = $access->get_operators($id_owner);
        $assigned_operators = OperatorGroupMan::Instance()->get_operators($id);
        
        $available_items = array();
        $assigned_items = array();
        
        if ($assigned_operators)
        {
            if ($available_operators)
            {
                $available_operators = array_diff_key($available_operators, $assigned_operators);
            }
            
            foreach ($assigned_operators as $operator)
            {
                $assigned_items[$operator->get_id()] = $operator->get_name();
            }
        }
        
        if ($available_operators)
        {
            foreach ($available_operators as $operator)
            {
                $available_items[$operator->get_id()] = $operator->get_name();
            }
        }
        
        $this->set_arg('available_operators', $available_items);
        $this->set_arg('assigned_operators', $assigned_items);
        
        // Locations ----------------------------------------------------
        
        $available_zones = $access->get_zones($id_owner);
        $assigned_zones = ZoneMan::Instance()->get_by_operator_group($id);
        
        $available_items = array();
        $assigned_items = array();
        
        if ($assigned_zones)
        {
            if ($available_zones)
            {
                $available_zones = array_diff_key($available_zones, $assigned_zones);
            }
            
            foreach ($assigned_zones as $zone)
            {
                $assigned_items[$zone->get_id()] = $zone->get_name();
            }
        }
        
        if ($available_zones)
        {
            foreach ($available_zones as $zone)
            {
                $available_items[$zone->get_id()] = $zone->get_name();
            }
        }
        
        $this->set_arg('available_zones', $available_items);
        $this->set_arg('assigned_zones', $assigned_items);
        
        $this->enable_post(true);
    }
   
    /** Process the form */
    protected function process()
    {
        $id = (int)$this->get_value('id');
        $id_owner = (int)$this->get_value('id_owner');
        $name = $this->get_value('name');
        $desc = null; // TODO
        
        $label = Operator::label();
        
        $assigned_operator_ids = $this->get_value('assigned_operator_ids');
        $assigned_operator_ids = $assigned_operator_ids ? explode(',', $assigned_operator_ids) : array();
        
        $assigned_zone_ids = $this->get_value('assigned_zone_ids');
        $assigned_zone_ids = $assigned_zone_ids ? explode(',', $assigned_zone_ids) : array();
        
        $group = OperatorGroupMan::Instance()->get_ledger($id);
        $group->set_name($name);
        $group->set_desc($desc);
        if (!$id) $group->set_owner_id($id_owner);
        
        if (ServerAdmin::Instance()->edit_operator_group($group, $assigned_operator_ids, $assigned_zone_ids))
        {
            $action = $id ? 'updated' : 'added';
            CHtmlPage::set_last_info("{$label} Group '{$name}' was {$action}");
            return true;
        }
        
        CHtmlPage::set_last_error("Failed to save {$label} Group '{$name}'. Verify that the name is unique");
        return false;
    }
}
?>