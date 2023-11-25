<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for updatable entity - updates timestamp after editing db record */
abstract class Updatable extends CDbRecord
{
    private static $m_ufd = array('ts_updated'=>'int');
    
    /** The constructor */
    public function __construct($table_name, $id)
    {
        parent::__construct($table_name, $id);
    }
    
    /** Returns field definitions for the table */
    public function get_field_def()
    {
        return self::$m_ufd;
    }
    
    /** Add fields to the database. Returns primary key (id) */
    public function write($field = null, $force_id = false)
    {
        $this->m_fields['ts_updated'] = time();
        $id = parent::write($field, $force_id);
        
        // Not all fields were written - write the update field
        if ($field) parent::write('ts_updated');
        
        return $id;
    }
    
    public function get_ts_updated() { return $this->m_fields['ts_updated']; }
    public function set_ts_updated($arg, $save = false) { $this->set_field('ts_updated', $arg, $save); }
}
?>