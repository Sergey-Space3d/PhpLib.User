<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Defines the operator group */
class OperatorGroup extends CDbLedger
{
    private static $m_ogfd = array('owner_id'=>'int');
    
    /** Returns field definitions for the table */
    public function get_field_def()
    {
        return array_merge(parent::get_field_def(), self::$m_ogfd);
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
        return parent::verify() && $this->m_fields['owner_id'] != 0;
    }
    
    /** Returns duplicate condition */
    public function get_duplicate_condition()
    {
        $condition = parent::get_duplicate_condition();
        $condition .= " AND owner_id={$this->m_fields['owner_id']}";
        return $condition;
    }
    
    public function get_owner_id() { return (int)$this->m_fields['owner_id']; }
    public function set_owner_id($arg, $save = false) { $this->set_field('owner_id', (int)$arg, $save); }
}
?>