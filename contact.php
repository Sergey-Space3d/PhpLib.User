<?php 
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Contact Class */
abstract class Contact extends Loggable
{
	private static $m_cfd = array(
	'location_id'=>'int',
    'external_id'=>'varchar(16)',
    'middle_name'=>'varchar(64)',
    'last_name'=>'varchar(64)',
	'email'=>'varchar(64)',
    'phone'=>'varchar(20)',
    'timezone'=>'varchar(32)');
	
	const AGREED_WITH_SERVICE = 0x100000;
	const CHANGE_PASSWORD     = 0x200000;
	const SMS_PREFERENCE      = 0x400000;
	const EMAIL_PREFERENCE    = 0x800000;
	
	/** The constructor */
	public function __construct($id = 0)
	{
	    parent::__construct(UserDb::get_name().'.contacts', $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{
		return array_merge(parent::get_field_def(), self::$m_cfd);
	}
	
	/** Called when writing new record to the database */
	protected function on_write_new() 
	{
	    $this->field_to_int('location_id');
	}
	
	/** Returns true if the record is valid */
	public function verify()
	{
		return parent::verify() && ($this->m_fields['email'] || $this->m_fields['phone']);
	}
	
	/** Normalize phone number */
	public static function normalize_phone($phone)
	{
	    if ($phone)
	    {
	        // Eliminate every char except 0-9
	        $phone = preg_replace("/[^0-9]/", '', $phone);
	        
	        // Eliminate leading 1 if its there
	        if (strlen($phone) == 11)
	        {
	            $phone = preg_replace("/^1/", '', $phone);
	        }
	        
	        return $phone;
	    }
	    
	    return $phone;
	}
	
	// Getters --------------------------------------------------------------
	
	/** Returns contact's full name */
	public function get_fullname($middle = false) 
	{
	    $arr = array($this->m_fields['name']);
	    if ($middle && $this->m_fields['middle_name']) $arr[] = $this->m_fields['middle_name'];
	    if ($this->m_fields['last_name']) $arr[] = $this->m_fields['last_name'];

	    return implode(' ', $arr);
	}
	
	public function get_location_id() { return (int)$this->m_fields['location_id']; }
	public function get_external_id() { return $this->m_fields['external_id']; }
	public function get_middle_name() { return $this->m_fields['middle_name']; }
	public function get_last_name() { return $this->m_fields['last_name']; }
	public function get_email() { return $this->m_fields['email']; }
	public function get_phone() { return $this->m_fields['phone']; }
	public function get_timezone() { return $this->m_fields['timezone']; }
	
	// Setters --------------------------------------------------------------
	
	public function set_location_id($arg, $save = false) { $this->set_field('location_id', (int)$arg, $save); }
	public function set_external_id($arg, $save = false) { $this->set_field('external_id', $arg, $save); }
	public function set_middle_name($arg, $save = false) { $this->set_field('middle_name', $arg, $save); }
	public function set_last_name($arg, $save = false) { $this->set_field('last_name', $arg, $save); }
	public function set_email($arg, $save = false) { $this->set_field('email', $arg, $save); }
	public function set_phone($arg, $save = false) { $this->set_field('phone', self::normalize_phone($arg), $save); }
	public function set_timezone($arg, $save = false) { $this->set_field('timezone', $arg, $save); }
}
?>