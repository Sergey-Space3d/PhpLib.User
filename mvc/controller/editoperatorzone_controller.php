<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Zone Controller */
class EditOperatorZoneController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $operator = new Operator($id);
        
        $name = "{$operator->get_name()} {$operator->get_last_name()}";
        $this->set_arg('name', $name);
        
        $access = LoggableAccess::Instance();
        $available_zones = $access->get_zones($operator->get_owner_id());
        $assigned_zones = ZoneMan::Instance()->get_by_operator($id);
        
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
        
        $access_all_locations = AccessMan::Instance()->has_access(
            $operator->get_id(), OperatorAccess::ACCESS_ALL_LOCATIONS);
        $this->set_arg('access_all_locations', $access_all_locations);
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = $this->get_value('id');
        $assigned_ids = $this->get_value('assigned_zone_ids');
        $assigned_ids = $assigned_ids ? explode(',', $assigned_ids) : array();
        $access_all_locations = $this->get_value('access_all_locations');
        
        $operator = new Operator($id);
        ZoneMan::Instance()->remove_operators(null, $id);
        
        if ($assigned_ids && !$access_all_locations)
        {
            ZoneMan::Instance()->add_operators($assigned_ids, $id);
        }
        
        // Update operator access
        if ($access_all_locations) AccessMan::Instance()->add_access($id, OperatorAccess::ACCESS_ALL_LOCATIONS);
        else AccessMan::Instance()->remove_access($id, OperatorAccess::ACCESS_ALL_LOCATIONS);
        
        // This will force request update
        $operator->touch();
        
        CHtmlPage::set_last_info("Zone access is updated for {$operator->get_name()} {$operator->get_last_name()}");
        return true;
    }
}
?>