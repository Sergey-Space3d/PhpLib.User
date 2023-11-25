<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Location Manager Class */
class LocationMan extends CDbRecordManSingleton
{
	/** The constructor */
    protected function __construct()
	{
	    parent::__construct(UserDb::get_name().'.locations', 'Location');
	}

	/** Initialize db dependencies */
	protected function init_dependencies() 
	{
	    $this->add_id_reference(UserDb::get_name().'.zone_to_location', 'id2');
	    $this->add_id_reference(UserDb::get_name().'.contacts', 'location_id', false);
	    $this->add_id_reference(UserDb::get_name().'.operators', 'location_id', false);
	    $this->add_id_reference(UserDb::get_name().'.owners', 'location_id', false);
	}
	
	/** Return locations by owner's id */
	public function get_by_owner($owner_id, $return_instances = true)
	{
	    $condition = $owner_id != 0 ? "owner_id={$owner_id}" : 'owner_id!=0';
	    $result = $this->m_table->get($condition);
	    return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
	}
	
	/** Inquire if the location's name is reserved */
	public function is_name_reserved($name, $location_id, $owner_id)
	{
	    $condition = "name='{$name}' AND owner_id={$owner_id} AND id!={$location_id}";
	    return $this->m_table->has($condition);
	}
	
	/** Inquire if the location's external id is reserved */
	public function is_external_id_reserved($external_id, $location_id, $owner_id)
	{
	    $condition = "external_id='{$external_id}' AND owner_id={$owner_id} AND id!={$location_id}";
	    return $this->m_table->has($condition);
	}
	
	/** Returns structure locations (without unit numbers) */
	public function filter_structures(array $locations = null)
	{
	    if (!$locations) return $locations;
	    $filtered = array();
	    
	    foreach ($locations as $key=>$location)
	    {
	        if (!$location->get_unit_number()) $filtered[$key] = $location;
	    }
	    
	    return $filtered;
	}
}
?>