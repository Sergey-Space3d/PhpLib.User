<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Operator Group Manager Class */
class OperatorGroupMan extends CDbLedgerMan
{
    /** The constructor */
    protected function __construct()
    {
        $table_name = UserDb::get_name().'.operatorgroup_ledgers';
        $map_table_name = UserDb::get_name().'.operatorgroup_to_operator';
        
        parent::__construct($table_name, $map_table_name, 'Operator', 'OperatorGroup');
    }
    
    /** Initialize db dependencies */
    protected function init_dependencies() 
    {
        $this->add_id_reference(UserDb::get_name().'.operatorgroup_to_operator', 'id1');
        $this->add_id_reference(UserDb::get_name().'.zone_to_operatorgroup', 'id2');
    }
    
    /** Remove ledger items */
    public function clear($group)
    {
        $group_id = is_numeric($group) ? $group : $this->get_ledger_by_name($group);
        $this->unmap($group_id, null);
        
        ZoneMan::Instance()->remove_operator_groups(null, $group_id);
    }
    
    /** Returns groups for specified owner */
    public function get_by_owner($owner_id, $return_instances = true)
    {
        $condition = "owner_id={$owner_id}";
        $result = $this->m_table->get($condition);
        return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
    }
    
    /** Returns groups for specified operator */
    public function get_by_operator($operator_id, $return_instances = true)
    {
        return $this->get_by_item($operator_id, $return_instances);
    }
    
    // Operators ------------------------------------------------------------
    
    /** Get group operators */
    public function get_operators($group, $return_instances = true)
    {
        $group_id = is_numeric($group) ? $group : $this->get_ledger_by_name($group);
        return $this->get_by_ledger($group_id, $return_instances);
    }
    
    /** Add operators to the group */
    public function add_operators($group, array $operator_ids)
    {
        $group_id = is_numeric($group) ? $group : $this->get_ledger_by_name($group);
        $this->map($group_id, $operator_ids);
    }
    
    /** Remove operators from the group */
    public function remove_operators($group, array $operator_ids = null)
    {
        $group_id = is_numeric($group) ? $group : $this->get_ledger_by_name($group);
        $this->unmap($group_id, $operator_ids);
    }
}
?>