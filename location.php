<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Location Class */
class Location extends Blockable
{
	private static $m_fd = array(
	'owner_id'=>'int',
    'external_id'=>'varchar(16)',
    'street_number'=>'varchar(10)',
	'street_name'=>'varchar(50)',
	'unit_number'=>'varchar(16)',
	'city'=>'varchar(25)',
	'state'=>'char(2)',
	'zip'=>'varchar(10)',
	'country'=>'char(2)',
	'latitude'=>'float',
	'longitude'=>'float');
	
	/** The constructor */
	public function __construct($id = 0)
	{
	    parent::__construct(UserDb::get_name().'.locations', $id);
	}

	/** Returns field definitions for the table */
	public function get_field_def()
	{
		return array_merge(parent::get_field_def(), self::$m_fd);
	}
	
	/** Called when writing new record to the database */
	protected function on_write_new() 
	{
	    $this->m_fields['owner_id'] = (int)$this->m_fields['owner_id'];
	    $this->m_fields['latitude'] = (int)$this->m_fields['latitude'];
		$this->m_fields['longitude'] = (int)$this->m_fields['longitude'];
	}

	/** Returns true if the record is valid */
	public function verify()
	{
		// TODO
		return (strlen($this->m_fields['country']) == 2);
	}

	/** Get textual representation of the address */
	function get_full_address($break=false, $country=true, $unit_number=null)
	{
		$address = $this->m_fields['street_number'].' '.$this->m_fields['street_name'];
		if ($unit_number === null) $unit_number = $this->m_fields['unit_number'];
		if ($unit_number) $address .= ", {$unit_number}";
		
		if ($break && $address) $b = '<br />'; else if ($address) $b = ', ';
		if ($this->m_fields['city'])  $address .= $b.$this->m_fields['city'];
		if ($this->m_fields['state']) $address .= ', '.$this->m_fields['state'];
		if ($this->m_fields['zip'])   $address .= ' '.$this->m_fields['zip'];
		if ($country) $address .= ' '.$this->m_fields['country'];

		return trim($address);
	}
		
	// Getters --------------------------------------------------------------

	public function get_owner_id()     { return (int)$this->m_fields['owner_id']; }
	public function get_external_id()  { return $this->m_fields['external_id']; }
	public function get_street_number(){ return $this->m_fields['street_number']; }
	public function get_street_name()  { return $this->m_fields['street_name']; }
	public function get_unit_number()  { return $this->m_fields['unit_number']; }
	public function get_city()         { return $this->m_fields['city']; }
	public function get_state()        { return $this->m_fields['state']; }
	public function get_zip()          { return $this->m_fields['zip']; }
	public function get_country()      { return $this->m_fields['country']; }
	
	public function get_latitude()     { return (float)$this->m_fields['latitude']; }
	public function get_longitude()    { return (float)$this->m_fields['longitude']; }
	
	// Setters --------------------------------------------------------------

	public function set_owner_id($arg, $save = false)     { $this->set_field('owner_id', (int)$arg, $save); }
	public function set_external_id($arg, $save = false)  { $this->set_field('external_id', $arg, $save); }
	public function set_street_number($arg, $save = false){ $this->set_field('street_number', $arg, $save); }
	public function set_street_name($arg, $save = false)  { $this->set_field('street_name', $arg, $save); }
	public function set_unit_number($arg, $save = false)  { $this->set_field('unit_number', $arg, $save); }
	public function set_city($arg, $save = false)         { $this->set_field('city', $arg, $save); }
	public function set_state($arg, $save = false)        { $this->set_field('state', $arg, $save); }
	public function set_zip($arg, $save = false)          { $this->set_field('zip', $arg, $save); }
	public function set_country($arg, $save = false)      { $this->set_field('country', $arg, $save); }
	
	public function set_latitude($arg, $save = false)     { $this->set_field('latitude', (float)$arg, $save); }
	public function set_longitude($arg, $save = false)    { $this->set_field('longitude', (float)$arg, $save); }
}
?>