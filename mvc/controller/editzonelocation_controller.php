<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Zone Location Controller */
class EditZoneLocationController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = (int)$this->get_arg('id');
        $id_owner = (int)$this->get_arg('id_owner');
        $zone = new Zone($id);
        
        $name = $zone->get_name();
        $this->set_arg('name', $name);
        
        $access = LoggableAccess::Instance();
        $available_locations = $access->get_locations($id_owner);
        $assigned_locations = ZoneMan::Instance()->get_locations($id);
        
        $available_items = array();
        $assigned_items = array();
        
        if ($assigned_locations)
        {
            if ($available_locations)
            {
                $available_locations = array_diff_key($available_locations, $assigned_locations);
            }
            
            foreach ($assigned_locations as $location)
            {
                $assigned_items[$location->get_id()] = $location->get_name();
            }
        }
        
        if ($available_locations)
        {
            foreach ($available_locations as $location)
            {
                $available_items[$location->get_id()] = $location->get_name();
            }
        }
        
        $this->set_arg('available_locations', $available_items);
        $this->set_arg('assigned_locations', $assigned_items);
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = $this->get_value('id');
        $assigned_ids = $this->get_value('assigned_location_ids');
        $assigned_ids = $assigned_ids ? explode(',', $assigned_ids) : array();
        
        $zone = new Zone($id);
        ZoneMan::Instance()->remove_locations($id);
        
        if ($assigned_ids)
        {
            ZoneMan::Instance()->add_locations($id, $assigned_ids);
            
            $location = new Location(current($assigned_ids));
            $timezone = TimeZone::get_timezone_by_state($location->get_state());
            
            if ($zone->get_timezone() != $timezone)
            {
                // Update timezone
                $zone->set_timezone($timezone, true);
            }
        }
        
        $label = Zone::label();
        
        CHtmlPage::set_last_info("Location's group is updated for {$label} '{$zone->get_name()}'");
        return true;
    }
}
?>