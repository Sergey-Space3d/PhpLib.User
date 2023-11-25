<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Zone Class */
class Zone extends Blockable
{
	const DEFAULT_NAME = 'Default';
	
	private static $m_fd = array(
    'owner_id'=>'int',
    'external_id'=>'varchar(16)',
    'timezone'=>'varchar(32)',
	); 
	
	/** The constructor */
	public function __construct($id = 0)
	{
	    parent::__construct(UserDb::get_name().'.zones', $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{ 
		return array_merge(parent::get_field_def(), self::$m_fd);
	}
	
	/** Called when writing new record to the database */
	protected function on_write_new() 
	{
	    $this->field_to_int('owner_id');
	    
	    if (!$this->m_fields['timezone'])
	    {
	        $this->m_fields['timezone'] = 'UTC';
	    }
	}

	/** Returns true if the record is valid */
	public function verify()
	{
	    return $this->m_fields['name'] && $this->m_fields['owner_id'] != 0;
	}
	
	public function get_owner_id() { return (int)$this->m_fields['owner_id']; }
	public function get_external_id() { return $this->m_fields['external_id']; }
	public function get_timezone() { return $this->m_fields['timezone']; }
	
	public function set_owner_id($arg, $save = false) { $this->set_field('owner_id', (int)$arg, $save); }
	public function set_external_id($arg, $save = false) { $this->set_field('external_id', $arg, $save); }
	public function set_timezone($arg, $save = false) { $this->set_field('timezone', $arg, $save); }
}
?>