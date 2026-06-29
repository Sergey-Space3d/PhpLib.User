<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Access Manager Class */
class AccessMan extends CDbLedgerMan
{
    /** The constructor */
    protected function __construct()
    {
        $table_name = UserDb::get_name().'.access_ledgers';
        $map_table_name = UserDb::get_name().'.access_to_operator';
        
        parent::__construct($table_name, $map_table_name, 'Operator');
    }
    
    // Access ---------------------------------------------------------------
    
    /** Add operator's access */
    public function add_access($operator_id, $access)
    {
        $accesses = is_array($access) ? $access : array($access);
        
        foreach ($accesses as $access)
        {
            $access_id = is_numeric($access) ? $access : $this->get_ledger_by_name($access);
            $this->map($access_id, $operator_id);
        }
    }
    
    /** Remove operator's access */
    public function remove_access($operator_id, $access)
    {
        $accesses = is_array($access) ? $access : array($access);

        foreach ($accesses as $access)
        {
            $access_id = is_numeric($access) ? $access : $this->get_ledger_by_name($access);
            $this->unmap($access_id, $operator_id);
        }
    }
    
    /** Returns true if operator has access specified, false otherwise */
    public function has_access($operator_id, $access)
    {
        $access_id = is_numeric($access) ? $access : $this->get_ledger_by_name($access);
        return $this->has($access_id, $operator_id);
    }
    
    /** Returns instances, or associative array where key is an access id, and value is an access name */
    public function get_access($operator_id, $is_admin, $return_instances = true)
    {
        $ledgers = $is_admin ? $this->get() : $this->get_by_item($operator_id);
        if ($return_instances) return $ledgers;
        
        $ret = array();
        
        if ($ledgers) foreach ($ledgers as $ledger)
        {
            $ret[$ledger->get_id()] = $ledger->get_name();
        }
        
        return $ret;
    }
    
    /** Returns array of operators with specified access */
    public function filter_access(array $operators, $access)
    {
        $filtered = array();
        $access_id = is_numeric($access) ? $access : $this->get_ledger_by_name($access);
        
        foreach ($operators as $key=>$operator)
        {
            $operator_id = ($operator instanceof CDbRecord) ? $operator->get_id() : $operator;
            if ($this->has_access($operator_id, $access_id)) $filtered[$key] = $operator;
        }
        
        return $filtered;
    }
    
    /** Filter array of ledgers. Returns only links, or only non-links, as array of ledger instances, or names */
    public function filter_links(array $ledgers, $return_links, $return_instances = true)
    {
        $filtered = array();
        
        $find_link = function($str)
        { return strripos($str, 'http://') === 0 || strripos($str, 'https://') === 0  || strripos($str, 'www.') === 0; };
        
        foreach ($ledgers as $key=>$ledger)
        {
            $is_link = $find_link($ledger->get_desc());
            if (!$is_link xor $return_links) $filtered[$key] = $return_instances ? $ledger : $ledger->get_name();
        }
        
        return $filtered;
    }
    
    // Access level ---------------------------------------------------------
    
    /** Add access level. Returns true on success */
    public function add_access_level($level, array $accesses)
    {
        try
        {
            foreach ($accesses as $access)
            {
                $id = is_numeric($access) ? $access : $this->get_ledger_by_name($access);
                $result = $this->m_table->get("id={$id}", null, false, 1);

                if ($result)
                {
                    $row = current($result);
                    $flags = (int)$row['flags'];
                    
                    if ($flags == 0 || $flags > $level)
                    {
                        $this->m_table->update($id, array('flags'=>$level));
                    }
                }
            }
        }
        catch (Exception $e) { return false; }
        
        return true;
    }
    
    /** Remove access level. Returns true on success */
    public function remove_access_level($level)
    {
        try
        {
            $result = $this->m_table->get("flags={$level}");

            if ($result) foreach ($result as $row)
            {
                if ($row['flags'] <= $level)
                {
                    $this->m_table->update($row['id'], array('flags'=>0));
                }
            }
        }
        catch (Exception $e) { return false; }
        
        return true;
    }
    
    /** Returns true if the access level exists */
    public function has_access_level($level)
    {
        return $this->m_table->has("flags={$level}");
    }
    
    /** Get access names for the level */
    public function get_access_level($level)
    {
        $accesses = array();
        $result = $this->m_table->get("flags<={$level}");
        
        if ($result) foreach ($result as $row)
        {
            if ($row['flags'])
            {
                $accesses[] = $row['_name'];
            }
        }
        
        return $accesses;
    }
}
?>