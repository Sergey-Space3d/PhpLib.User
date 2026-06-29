<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Owner Manager Class */
class OwnerMan extends CDbRecordManSingleton
{	
	/** The constructor */
    protected function __construct()
	{
	    parent::__construct(UserDb::get_name().'.owners', 'Owner');
	}
	
	/** Initialize db dependencies */
	protected function init_dependencies() 
	{
	    $fn = function ($id)
	    {
	        // Delete the locations
	        $location_ids = LocationMan::Instance()->get_by_owner($id, false);
	        if ($location_ids) foreach ($location_ids as $location_id) LocationMan::Instance()->delete($location_id);
	        
	        // Delete the zones
	        $zone_ids = ZoneMan::Instance()->get_by_owner($id, false);
	        if ($zone_ids) foreach ($zone_ids as $zone_id) ZoneMan::Instance()->delete($zone_id);
	        
	        // Delete the operators
	        $operator_ids = OperatorMan::Instance()->get_by_owner($id, false);
	        if ($operator_ids) foreach ($operator_ids as $operator_id) OperatorMan::Instance()->delete($operator_id);
	        
	        // Delete the operator groups
	        $op_group_ids = OperatorGroupMan::Instance()->get_by_owner($id, false);
	        if ($op_group_ids) foreach ($op_group_ids as $op_group_id) OperatorGroupMan::Instance()->delete($op_group_id);
	    };
	    
	    // Add on delete callback
	    $this->add_on_delete_callback($fn);
	}
	
	/** Get owner by location's id */
	public function get_by_location($location_id)
	{
	    $location = new Location($location_id);
	    $owner_id = $location->get_owner_id();
	    return $owner_id ? new Owner($owner_id) : null;
	}
	
	/** Inquire if the owner's name is reserved */
	public function is_name_reserved($name, $owner_id)
	{
	    return is_array($this->m_table->get_fields("name='{$name}' AND id!={$owner_id}", null, 'id'));
	}
}
?>