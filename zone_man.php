<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Zone Manager Class */
class ZoneMan extends CDbRecordManSingleton
{
    protected $location_map = null;
    protected $operator_map = null;
    protected $operator_group_map = null;
    
    /** The constructor */
    protected function __construct()
	{
	    parent::__construct(UserDb::get_name().'.zones', 'Zone');
	}
	
	/** Initialize db dependencies */
	protected function init_dependencies() 
	{
	    $map_name = UserDb::get_name().'.zone_to_location';
	    $this->location_map = new CDbMapMan($map_name);
	    $this->add_id_reference($map_name, 'id1');
	    
	    $map_name = UserDb::get_name().'.zone_to_operator';
	    $this->operator_map = new CDbMapMan($map_name);
	    $this->add_id_reference($map_name, 'id1');
	    
	    $map_name = UserDb::get_name().'.zone_to_operatorgroup';
	    $this->operator_group_map = new CDbMapMan($map_name);
	    $this->add_id_reference($map_name, 'id1');
	}
	
	/** Get zones by owner. The owner could be id, or instance */
	public function get_by_owner($owner, $return_instances = true)
	{
	    $owner_id = ($owner instanceof CDbRecord) ? $owner->get_id() : $owner;
	    $result = $this->m_table->get("owner_id={$owner_id}");
	    return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
	}

	// Locations ------------------------------------------------------------
	
	/** Get zones by location(s). The location could be id, instance, or array */
	public function get_by_location($location, $return_instances = true)
	{
	    $zones = array();
	    
	    foreach ($this->to_id_array($location) as $location)
	    {
	        foreach ($this->location_map->get_by_id2($location) as $map)
	        {
	            $id = $map->get_id1();
	            $zones[$id] = $return_instances ? new $this->m_class_name($id) : $id;
	        }
	    }
	    
	    return $zones;
	}
	
	/** Get locations by zone  (may be id, or instance) */
	public function get_locations($zone, $return_instances = true)
	{
	    $locations = array();
	    
	    foreach ($this->to_id_array($zone) as $zone)
	    {
	        foreach ($this->location_map->get_by_id1($zone) as $map)
            {
                $id = $map->get_id2();
                $locations[$id] = $return_instances ? new Location($id) : $id;
            }
	    }
	    
	    return $locations;
	}
	
	/** Returns true if zone has the location (may be id, or instance) */
	public function has_location($zone, $location)
	{
	    return $this->location_map->has($zone, $location);
	}
	
	/** Add locations to the zone (args may be id, instance, or array) */
	public function add_locations($zones, $locations)
	{
	    $this->location_map->map($zones, $locations);
	}
	
	/** Remove locations from the zone (locations may be id, instance, or array) */
	public function remove_locations($zone_id, $locations = null)
	{
	    $this->location_map->unmap($zone_id, $locations);
	}
	
	// Operators ------------------------------------------------------------
	
	/** Get zones by operator(s). The operator could be id, instance, or array */
	public function get_by_operator($operator, $use_groups = false, $return_instances = true)
	{
	    $zones = array();
	    
	    foreach ($this->to_id_array($operator) as $operator)
	    {
	        foreach ($this->operator_map->get_by_id2($operator) as $map)
	        {
	            $id = $map->get_id1();
	            $zones[$id] = $return_instances ? new $this->m_class_name($id) : $id;
	        }
	        
	        if ($use_groups)
	        {
	            // Use operator groups
	            $group_ids = OperatorGroupMan::Instance()->get_by_operator($operator, false);
	            $zones += $this->get_by_operator_group($group_ids, $return_instances);
	        }
	    }

	    return $zones;
	}
	
	/** Get operators by zone (may be id, or instance) */
	public function get_operators($zone, $use_groups = false, $return_instances = true)
	{
	    $operators = array();

	    foreach ($this->to_id_array($zone) as $zone)
	    {
	        foreach ($this->operator_map->get_by_id1($zone) as $map)
    	    {
    	        $id = $map->get_id2();
    	        $operators[$id] = $return_instances ? new Operator($id) : $id;
    	    }
	    }
	    
	    if ($use_groups)
	    {
	        foreach ($this->operator_group_map->get_by_id1($zone) as $map)
	        {
	            $id = $map->get_id2(); // group's id
	            $operators += OperatorGroupMan::Instance()->get_operators($id, $return_instances);
	        }
	    }
	    
	    return $operators;
	}
	
	/** Returns true if zone has the operator (may be id, or instance) */
	public function has_operator($zone, $operator)
	{
	    return $this->operator_map->has($zone, $operator);
	}
	
	/** Add operators to the zone (args may be id, instance, or array) */
	public function add_operators($zones, $operators)
	{
	    $this->operator_map->map($zones, $operators);
	}
	
	/** Remove operators from the zone (operators may be id, instance, or array) */
	public function remove_operators($zone_id, $operators = null)
	{
	    $this->operator_map->unmap($zone_id, $operators);
	}
	
	// Operator Groups ------------------------------------------------------
	
	/** Get zones by operator group(s). The operator_group could be id, instance, or array */
	public function get_by_operator_group($operator_group, $return_instances = true)
	{
	    $zones = array();
	    
	    foreach ($this->to_id_array($operator_group) as $operator_group)
	    {
	        foreach ($this->operator_group_map->get_by_id2($operator_group) as $map)
	        {
	            $id = $map->get_id1();
	            $zones[$id] = $return_instances ? new $this->m_class_name($id) : $id;
	        }
	    }
	    
	    return $zones;
	}
	
	/** Get operator groups by zone  (may be id, or instance) */
	public function get_operator_groups($zone)
	{
	    $operator_groups = array();

	    foreach ($this->to_id_array($zone) as $zone)
	    {
	        foreach ($this->operator_group_map->get_by_id1($zone) as $map)
    	    {
    	        $id = $map->get_id2();
    	        $operator_groups[$id] = $id;
    	    }
	    }
	    
	    return $operator_groups;
	}
	
	/** Returns true if zone has the operator_group (may be id, or instance) */
	public function has_operator_group($zone, $operator_group)
	{
	    return $this->operator_group_map->has($zone, $operator_group);
	}
	
	/** Add operator groups to the zone (args may be id, instance, or array) */
	public function add_operator_groups($zones, $operator_groups)
	{
	    $this->operator_group_map->map($zones, $operator_groups);
	}
	
	/** Remove operator groups from the zone (operator_groups may be id, instance, or array) */
	public function remove_operator_groups($zone_id, $operator_groups = null)
	{
	    $this->operator_group_map->unmap($zone_id, $operator_groups);
	}
}
?>