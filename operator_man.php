<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Operator Manager Class */
class OperatorMan extends ContactMan
{
	/** The constructor */
    protected function __construct()
	{
	    CDbRecordManSingleton::__construct(UserDb::get_name().'.operators', 'Operator');
	}
	
	/** Initialize db dependencies */
	protected function init_dependencies() 
	{
	    $this->add_id_reference(UserDb::get_name().'.zone_to_operator', 'id2');
	    $this->add_id_reference(UserDb::get_name().'.access_to_operator', 'id2');
	    $this->add_id_reference(UserDb::get_name().'.operatorgroup_to_operator', 'id2');
	}
	
	/** Get operators with admin priviliges */
	public function get_admins($return_instances = true)
	{
	    $flags = Operator::ADMIN;
	    $result = $this->m_table->get("(flags & {$flags})!=0");
	    return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
	}
	
	/** Get operators by owner's id */
	public function get_by_owner($owner_id, $return_instances = true)
	{
	    $condition = "owner_id={$owner_id}";
	    $result = $this->m_table->get($condition);
	    return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
	}
}
?>