<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** The operator is admin, or the owner's representative */
class Operator extends Contact
{
    private static $m_fd = array('owner_id'=>'int');
    
    // The operator's admin
	const ADMIN  = 0x2000000;	
	
	/** The constructor */
	public function __construct($id = 0)
	{
	    Loggable::__construct(UserDb::get_name().'.operators', $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{
		return array_merge(parent::get_field_def(), self::$m_fd);
	}

	/** Called when writing new record to the database */
	protected function on_write_new()
	{
	    parent::on_write_new();
	    $this->field_to_int('owner_id');
	}
	
	/** Returns true if the record is valid */
	public function verify()
	{
		return parent::verify();
	}
	
	/** Inquire if this is operator's admin */
	public function is_admin()
	{
		return $this->has_flag(Operator::ADMIN);
	}
	
	public function get_owner_id() { return (int)$this->m_fields['owner_id']; }
	public function set_owner_id($arg, $save = false) { $this->set_field('owner_id', (int)$arg, $save); }
}
?>