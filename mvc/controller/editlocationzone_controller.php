<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Location Zone Controller */
class EditLocationZoneController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = $this->get_arg('id');
        $location = new Location($id);
        
        $name = "{$location->get_name()}";
        $this->set_arg('name', $name);
        
        $access = LoggableAccess::Instance();
        $available_zones = $access->get_zones($location->get_owner_id());
        $assigned_zones = ZoneMan::Instance()->get_by_location($id);
        
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
        $id = $this->get_value('id');
        $assigned_ids = $this->get_value('assigned_zone_ids');
        $assigned_ids = $assigned_ids ? explode(',', $assigned_ids) : array();
        
        $location = new Location($id);
        ZoneMan::Instance()->remove_locations(null, $id);
        
        if ($assigned_ids)
        {
            ZoneMan::Instance()->add_locations($assigned_ids, $id);
        }

        // This will force request update
        $location->touch();
        
        CHtmlPage::set_last_info(Zone::label()." access is updated for {$location->get_name()}");
        return true;
    }
}
?>