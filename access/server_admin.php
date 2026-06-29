<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Server Admin Class */
class ServerAdmin
{
    /** Last error */
    private $m_error = null;
    
    /** Don't allow cloning */
    final private function __clone() {}
    
    /** Returns single instance of the class */
    final public static function Instance($class = null)
	{
	    static $instance = null;
	    
		if (!$instance)
		{
		    $instance = $class ? new $class : new static();
		}
		
		return $instance;
	}
	
	/** The constructor */
	protected function __construct()
	{
	}
	
    // Operators ------------------------------------------------------------

	/** Modify operator */
	public function edit_operator(Operator $operator)
	{
		return $operator->write();
	}
	
	/** Block/unblock the operator */
	public function block_operator($operator_id, $block)
	{
		$operator = new Operator($operator_id);
		if (!$operator->is_saved()) return false; // Invalid operator
		if (!($operator->is_blocked() xor $block)) return false; // Already in state
		
		// Block the operator
		$operator->block($block);
		return true;
	}
	
	/** Delete the operator, logout the group */
	public function delete_operator($operator_id)
	{
		// Delete the operator
		return OperatorMan::Instance()->delete($operator_id);
	}
	
    // Operator groups ------------------------------------------------------
	
	/** Modify operator group. Returns success state */
	public function edit_operator_group(OperatorGroup $group, array $operator_ids = null, array $zone_ids = null)
	{
	    if (!OperatorGroupMan::Instance()->is_duplicate($group)) 
	    {
	        $id = $group->write();
	        
	        if ($id)
	        {
	            OperatorGroupMan::Instance()->clear($id);
	            if ($operator_ids) OperatorGroupMan::Instance()->add_operators($id, $operator_ids);
	            if ($zone_ids) ZoneMan::Instance()->add_operator_groups($zone_ids, $id);
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	/** Delete operator group. Returns success state */
	public function delete_operator_group($group_id)
	{
	    return OperatorGroupMan::Instance()->delete($group_id);
	}
	
    // Owners ---------------------------------------------------------------
	
	/** Modify owner */
	public function edit_owner(Owner $owner)
	{
		return $owner->write();
	}
	
	/** Block/unblock the owner */
	public function block_owner($owner_id, $block)
	{
		$owner = new Owner($owner_id);
		if (!$owner->is_saved()) return false; // Invalid owner
		if (!($owner->is_blocked() xor $block)) return false; // Already in state
		
		// Block the owner
		$owner->block($block);
		return true;
	}
	
	/** Delete the owner */
	public function delete_owner($owner_id)
	{
	    $owner = new Owner($owner_id);
	    if (!$owner->is_saved()) return false; // Invalid owner
	    
		// Delete the owner
	    return OwnerMan::Instance()->delete($owner_id);
	}
	
    // Locations ------------------------------------------------------------
	
	/** Modify location */
	public function edit_location(Location $location, $zone_id = 0)
	{
	    $is_new = !$location->get_id();
	    $id = $location->write();
	    if ($is_new && $zone_id) ZoneMan::Instance()->add_locations($zone_id, $id);
	    return $id;
	}
	
	/** Block/unblock the location */
	public function block_location($location_id, $block)
	{
		$location = new Location($location_id);
		if (!$location->is_saved()) return false; // Invalid location
		if (!($location->is_blocked() xor $block)) return false; // Already in state
		
		// Block the location
		$location->block($block);
		return true;
	}
	
	/** Delete the location, logout the group */
	public function delete_location($location_id)
	{
	    $location = new Location($location_id);
	    if (!$location->is_saved()) return false; // Invalid location
	    
		// Delete the location
	    return LocationMan::Instance()->delete($location_id);
	}
	
    // Zones ----------------------------------------------------------------
	
	/** Modify zone */
	public function edit_zone(Zone $zone)
	{
		return $zone->write();
	}
	
	/** Block/unblock the zone */
	public function block_zone($zone_id, $block)
	{
		$zone = new Zone($zone_id);
		if (!$zone->is_saved()) return false; // Invalid zone
		if (!($zone->is_blocked() xor $block)) return false; // Already in state
		
		// Block the zone
		$zone->block($block);
		return true;
	}
	
	/** Delete the zone, logout the group */
	public function delete_zone($zone_id)
	{
	    $zone = new Zone($zone_id);
	    if (!$zone->is_saved()) return false; // Invalid zone
	    
	    // Delete the zone
		return ZoneMan::Instance()->delete($zone_id);
	}
	
	// Error ----------------------------------------------------------------
	
	/** Get last error */
	public function get_last_error() { return $this->m_error; }
	
	/** Set last error */
	protected function set_last_error($error) { $this->m_error = $error; }
}

?>