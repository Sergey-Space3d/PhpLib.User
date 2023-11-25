<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Contact Manager Class */
abstract class ContactMan extends CDbRecordManSingleton
{
	/** The constructor */
    protected function __construct()
	{
		parent::__construct(UserDb::get_name().'.contacts', 'Contact');
	}
	
	/** Make location filter */
	public function make_location_filter($location_id) { return "location_id={$location_id}"; }
	
	/** Get contacts by location's id */
	public function get_by_location($location_id, $return_instances = true)
	{
	    $result = $this->m_table->get("location_id={$location_id}", $this->m_sort_by);
	    return $return_instances ? $this->to_instances($result) : $this->to_ids($result);
	}
	
	/** Get contact by login and password */
	public function get_by_login($login, $password)
	{
	    $login = strtolower($login);
	    $condition = "lower(login)='{$login}' AND password='{$password}'";
	    $result = $this->m_table->get($condition, $this->m_sort_by, false, 1);
	    
	    return $result ? $this->to_instance(array_shift($result)) : null;
	}
	
	/** Returns true if the login is reserved */
	public function has_login($login, $contact_id = 0)
	{
	    $login = strtolower($login);
	    $condition = "lower(login)='{$login}'";
	    if ($contact_id) $condition .= " AND id!={$contact_id}";
	    
	    $result = $this->m_table->get($condition, $this->m_sort_by, true, 1);
	    return $result ? true : false;
	}
	
	/** Generate password */
	public function generate_password()
	{
	    // Upper char: 65-90; lower: 97-122
	    return chr(rand(65, 90)).chr(rand(97, 122)).rand(10, 99).
	           chr(rand(65, 90)).chr(rand(97, 122)).rand(10, 99).chr(rand(35, 38));
	}
	
	/** Find contact(s) by search string. Returns ids, or instances */
	public function search($search, $return_instances = true)
	{
	    $filter = $this->m_filter;
	    $arr = explode(' ', $search);
	    
	    foreach ($arr as $search)
	    {
	        $condition = "name LIKE '{$search}' OR last_name LIKE '{$search}' OR email LIKE '{$search}' OR phone LIKE '{$search}'";
	        $this->set_filter($this->merge_filter($condition));
	    }
	    
	    $ret = $this->get(null, $return_instances);
	    $this->set_filter($filter);
	    return $ret;
	}
}
?>