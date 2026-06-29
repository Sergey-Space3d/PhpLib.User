<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Owner Class */
class Owner extends Blockable
{
	private static $m_fd = array(
	'location_id'=>'int', // Residence or headquarter location (optional)
    'external_id'=>'varchar(16)',
    'email'=>'varchar(50)',
	'phone'=>'varchar(20)');

	/** The constructor */
	public function __construct($id = 0)
	{
	    parent::__construct(UserDb::get_name().'.owners', $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{
		return array_merge(parent::get_field_def(), self::$m_fd);
	}

	/** Called when writing new record to the database */
	protected function on_write_new() 
	{
		$this->m_fields['location_id'] = (int)$this->m_fields['location_id'];
	}
	
	/** Returns true if the record is valid */
	public function verify()
	{
	    return $this->m_fields['name'] && ($this->m_fields['email'] || $this->m_fields['phone']);
	}
	
	// Getters --------------------------------------------------------------
	
	public function get_location_id() { return (int)$this->m_fields['location_id']; }
	public function get_external_id() { return $this->m_fields['external_id']; }
	public function get_email() { return $this->m_fields['email']; }
	public function get_phone() { return $this->m_fields['phone']; }
	
	// Setters --------------------------------------------------------------
	
	public function set_location_id($arg, $save = false) { $this->set_field('location_id', (int)$arg, $save); }
	public function set_external_id($arg, $save = false) { $this->set_field('external_id', $arg, $save); }
	public function set_email($arg, $save = false) { $this->set_field('email', $arg, $save); }
	public function set_phone($arg, $save = false) { $this->set_field('phone', $arg, $save); }
}
?>