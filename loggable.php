<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for loggable entity (operator, user, etc.) */
abstract class Loggable extends Blockable
{
	private static $m_lfd = array(
	'login'=>'varchar(64) UNIQUE',
	'password'=>'varchar(32)',
    'ip_first'=>'varchar(24)',
    'ip_last'=>'varchar(24)',
	'referer'=>'varchar(128)',
	'ts_logged'=>'int'); 
	
	/** The constructor */
	public function __construct($table_name, $id)
	{
		parent::__construct($table_name, $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{
		return array_merge(parent::get_field_def(), self::$m_lfd);
	}
	
	/** Add fields to the database. Returns primary key (id) */
	public function write($field = null, $force_id = false)
	{
	    static $volatile = array('ts_logged', 'ip_first', 'ip_last', 'referer'); // volatile fields
	    return in_array($field, $volatile)  ? CDbRecord::write($field, $force_id) : parent::write($field, $force_id);
	}
	
	/** Called when writing new record to the database */
	protected function on_write_new()
	{
	    $this->field_to_int('ts_logged');
	}
	
	/** Returns true if the record is valid */
	public function verify()
	{
		return $this->m_fields['name'] && $this->m_fields['login'] && $this->m_fields['password'];
	}
	
	public function get_login() { return $this->m_fields['login']; }
	public function get_password() { return $this->m_fields['password']; }
	public function get_ip_first() { return $this->m_fields['ip_first']; }
	public function get_ip_last() { return $this->m_fields['ip_last']; }
	public function get_referer() { return $this->m_fields['referer']; }
	public function get_ts_logged() { return (int)$this->m_fields['ts_logged']; }
	
	public function set_login($arg, $save = false) { $this->set_field('login', strtolower($arg), $save); }
	public function set_password($arg, $save = false) { $this->set_field('password', $arg, $save); }
	public function set_ip_first($arg, $save = false) { $this->set_field('ip_first', $arg, $save); }
	public function set_ip_last($arg, $save = false) { $this->set_field('ip_last', $arg, $save); }
	public function set_referer($arg, $save = false) { $this->set_field('referer', $arg, $save); }
	public function set_ts_logged($arg, $save = false) { $this->set_field('ts_logged', (int)$arg, $save); }
}
?>