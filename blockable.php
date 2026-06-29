<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for blockable entity */
abstract class Blockable extends Updatable
{
	private static $m_bfd = array('name'=>'varchar(64)'); 
	
	const BLOCKED = 0x1000000;
	
	/** The constructor */
	public function __construct($table_name, $id)
	{
		parent::__construct($table_name, $id);
	}
	
	/** Returns field definitions for the table */
	public function get_field_def()
	{
	    return array_merge(parent::get_field_def(), self::$m_bfd);
	}
	
	/** Block/unblock the user */
	public function block($block)
	{
		$this->set_flag(Blockable::BLOCKED, $block, true);
	}
	
	/** Returns blocking status */
	public function is_blocked()
	{
		return $this->has_flag(Blockable::BLOCKED);
	}
	
	public function get_name() { return $this->m_fields['name']; }
	public function set_name($arg, $save = false) { $this->set_field('name', $arg, $save); }
}
?>