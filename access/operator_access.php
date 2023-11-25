<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Manages access of logged operator */
class OperatorAccess extends LoggableAccess
{
    // Admin panel access 
    const ADMIN_PANEL = 'Admin Panel';
    
    // Operator access
    const VIEW_OPERATORS   = 'View operators';
    const MODIFY_OPERATORS = 'Modify operators';
    const BLOCK_OPERATORS  = 'Block operators';
    const ADD_OPERATORS    = 'Add operators';
    const DELETE_OPERATORS = 'Delete operators';
    
    // Operator group access
    const VIEW_OPERATOR_GROUPS   = 'View operator groups';
    const MODIFY_OPERATOR_GROUPS = 'Modify operator groups';
    const ADDDEL_OPERATOR_GROUPS = 'Add/delete operator groups';
    
    // Owner access
    const VIEW_OWNERS   = 'View owners';
    const MODIFY_OWNERS = 'Modify owners';
    const BLOCK_OWNERS  = 'Block owners';
    const ADDDEL_OWNERS = 'Add/delete owners';
    
    // Location access
    const VIEW_LOCATIONS   = 'View locations';
    const MODIFY_LOCATIONS = 'Modify locations';
    const BLOCK_LOCATIONS  = 'Block locations';
    const ADDDEL_LOCATIONS = 'Add/delete locations';
    const ACCESS_ALL_LOCATIONS = 'Access all locations';
    
    // Zone access
    const VIEW_ZONES   = 'View zones';
    const MODIFY_ZONES = 'Modify zones';
    const BLOCK_ZONES  = 'Block zones';
    const ADDDEL_ZONES = 'Add/delete zones';
    
    /** Setup access (null for default). Returns access */
    public function setup_access(array $access = null, array $links = null)
    {
        if (!$access) $access = array(
            OperatorAccess::ADMIN_PANEL,
            OperatorAccess::VIEW_OPERATORS, OperatorAccess::MODIFY_OPERATORS, OperatorAccess::BLOCK_OPERATORS, OperatorAccess::ADD_OPERATORS, OperatorAccess::DELETE_OPERATORS,
            OperatorAccess::VIEW_OPERATOR_GROUPS, OperatorAccess::MODIFY_OPERATOR_GROUPS, OperatorAccess::ADDDEL_OPERATOR_GROUPS,
            OperatorAccess::VIEW_OWNERS, OperatorAccess::MODIFY_OWNERS, OperatorAccess::BLOCK_OWNERS, OperatorAccess::ADDDEL_OWNERS,
            OperatorAccess::VIEW_LOCATIONS, OperatorAccess::MODIFY_LOCATIONS, OperatorAccess::BLOCK_LOCATIONS, OperatorAccess::ADDDEL_LOCATIONS, OperatorAccess::ACCESS_ALL_LOCATIONS,
            OperatorAccess::VIEW_ZONES, OperatorAccess::MODIFY_ZONES, OperatorAccess::BLOCK_ZONES, OperatorAccess::ADDDEL_ZONES,
        );
        
        if (!is_array($links)) $links = array();
        
        foreach ($access as $at)
        {
            $ledger = AccessMan::Instance()->get_ledger(0);
            $ledger->set_name($at);
            $ledger->set_desc($links[$at]);
            $ledger->write();
        }
        
        return $access;
    }
    
    /** Get access array by level */
    protected function get_access($level)
    {
        return AccessMan::Instance()->get_access_level($level);
    }
    
    /** Get all access levels */
    protected function get_all_levels()
    {
        $levels = array();
        $level = 1;
        
        while (AccessMan::Instance()->has_access_level($level))
        {
            $levels[$level] = $this->get_level_name($level);
            $level++;
            
            if ($level > LoggableAccess::ACCESS_LEVEL_ADMIN)
            {
                break;
            }
        }
        
        if ($level > 1)
        {
            $levels[LoggableAccess::ACCESS_LEVEL_ADMIN] = $this->get_level_name(LoggableAccess::ACCESS_LEVEL_ADMIN);
        }
        
        return $levels;
    }
    
    /** Set access level. Returns true on success */
    public function set_access_level(Loggable $loggable, $level)
    {
        if (!($loggable instanceof Operator) || $loggable->get_id() == 0 || $level < 1 || $level > LoggableAccess::ACCESS_LEVEL_ADMIN)
        {
            return false;
        }
        
        $cur_level = $this->get_access_level($loggable);
        
        if ($cur_level == $level)
        {
            return true;
        }
        
        if ($cur_level > 0)
        {
            if ($cur_level == LoggableAccess::ACCESS_LEVEL_ADMIN)
            {
                $loggable->set_flag(Operator::ADMIN, false);
            }
            else
            {
                $access = $this->get_access($cur_level);
                AccessMan::Instance()->remove_access($loggable->get_id(), $access);
            }
        }
        
        if ($level == LoggableAccess::ACCESS_LEVEL_ADMIN)
        {
            $loggable->set_flag(LoggableAccess::ACCESS_LEVEL_MASK, false);
            $loggable->set_flag(Operator::ADMIN, true, true);
            return true;
        }
        
        $access = $this->get_access($level);
        AccessMan::Instance()->add_access($loggable->get_id(), $access);
        
        $loggable->set_flag(LoggableAccess::ACCESS_LEVEL_MASK, false);
        $loggable->set_flag($level, true, true);
        return true;
    }
    
    // Login ----------------------------------------------------------------

    /** Login */
    public function login($login, $password, $access = null)
    {
        $logged = OperatorMan::Instance()->get_by_login($login, $password);
        
        if ($logged)
        {
            if ($logged->is_blocked())
            {
                $this->set_last_error("Your access to site is restricted. Please contact admin for details");
                return false;
            }
            
            if (!$access)
            {
                // Switch to the required access
                $access = $_SESSION['__required_access__'];
            }
            
            if (!$logged->is_admin() && !AccessMan::Instance()->has_access($logged->get_id(), $access))
            {
                $this->set_last_error("You don't have access to this page. Please contact admin for details");
                return false;
            }
            
            // Reset session, set logged member
            $this->reset_session($logged, false);

            // Remember access
            $_SESSION['__loggable_access__'] = $access;
            
            // Remember access
            $_SESSION['__loggable_access_types__'] = AccessMan::Instance()->get_access($logged->get_id(), false, false);
            return true;
        }
        
        $this->set_last_error("Invalid login or password");
        return false;
        
    }

    /** Don't allow operator with different access to be logged */
    public function verify_access($access)
    {
        $_SESSION['__required_access__'] = $access;
        
        if ($this->m_logged)
        {
            if (!($this->m_logged instanceof Operator))
            {
                // Different kind of logged
                $this->logoff();
            }
            else if (!$this->is_admin($this->m_logged) && $_SESSION['__loggable_access__'] != $access)
            {
                if (AccessMan::Instance()->has_access($this->m_logged->get_id(), $access))
                {
                    // Switch access
                    $_SESSION['__loggable_access__'] = $access;
                }
                else 
                {
                    // Logoff - restricted access
                    $this->logoff();
                }
            }
        }
    }
    
    /** Recover by email or phone. Returns loggable instance on success, null otherwise */
    public function recover($email_or_phone)
    {
        $val = strtolower($email_or_phone);
        $condition = "lower(login)='{$val}' OR lower(email)='{$val}' OR phone='{$val}'";
        $result = OperatorMan::Instance()->get_table()->get($condition);
        
        if ($result)
        {
            $operator = new Operator();
            $operator->set(array_shift($result));
            return $operator;
        }
        
        return null;
    }
    
    /** Get loggable's timezone */
    public function get_timezone()
    {
        $tz = parent::get_timezone();
        if ($tz) return $tz;
        
        if ($this->m_logged instanceof Operator)
        {
            $owner = new Owner($this->m_logged->get_owner_id());
            
            if ($owner->get_location_id())
            {
                // Get owner state
                $location = new Location($owner->get_location_id());
                $_SESSION['__loggedtimezone__'] = $location->get_state();
                
            }
            else 
            {
                if (!$this->can(OperatorAccess::ACCESS_ALL_LOCATIONS))
                {
                    $locations = $this->get_locations($owner->get_id());
                    
                    if ($locations)
                    {
                        // Get location state
                        $location = array_pop($locations);
                        $_SESSION['__loggedtimezone__'] = $location->get_state();
                    }
                }
            }
        }
        
        if (!$_SESSION['__loggedtimezone__'])
        {
            // Set ET as default
            $_SESSION['__loggedtimezone__'] = 'America/New_York';
        }
        
        return $_SESSION['__loggedtimezone__'];
    }
    
    // Access ---------------------------------------------------------------
    
    /** Inquire if the operator can do the action */
    public function can($do, $obj = null)
    {
        if ($this->m_logged && !$this->m_logged->is_blocked())
        {
            if ($this->m_logged->is_admin() || in_array($do, $_SESSION['__loggable_access_types__']))
            {
                switch ($do)
                {
                    case OperatorAccess::DELETE_OPERATORS:
                    case OperatorAccess::BLOCK_OPERATORS:
                        if ($obj instanceof Operator)
                        {
                            return !$this->is_admin($obj) && $this->m_logged->get_id() != $obj->get_id();
                        }
                        break;
                    case OperatorAccess::MODIFY_OPERATORS:
                        if ($obj instanceof Operator)
                        {
                            if ($obj->get_id() == $this->get_logged()->get_id()) return true; // Can edit himself
                            return !$this->is_admin($obj) || $this->is_admin($this->m_logged);
                        }
                        break;
                        
                    default:
                        return true;
                }
            }
        }
        
        return false;
    }

    /** Get the operators visible by the current operator */
    public function get_operators($id_owner = 0, $id_zone = 0, $accesses = null, $return_instances = true)
    {
        $ops = array();
        
        if (!$this->is_blocked())
        {
            $op_owner_id = $this->m_logged->get_owner_id();
            $op_is_admin = $this->is_admin($this->m_logged);
            
            if ($op_is_admin || $op_owner_id == 0)
            {
                $get_all_location_ops = function($oid, $return_instances)
                {
                    $filtered = array();
                    
                    $wops = OperatorMan::Instance()->get_by_owner($oid, $return_instances);
                    if ($oid != 0) $wops += OperatorMan::Instance()->get_by_owner(0, $return_instances);
                    
                    if ($wops) foreach ($wops as $id=>$op)
                    {
                        if (AccessMan::Instance()->has_access($id, OperatorAccess::ACCESS_ALL_LOCATIONS))
                        {
                            $filtered[$id] = $op;
                        }
                    }
                    
                    return $filtered;
                };
                
                if ($id_zone != 0)
                {
                    $ops = ZoneMan::Instance()->get_operators($id_zone, true, $return_instances);
                    $ops += $get_all_location_ops($id_owner, $return_instances);
                }
                else if ($id_owner != 0)
                {
                    $ops = OperatorMan::Instance()->get_by_owner($id_owner, $return_instances);
                    $ops += $get_all_location_ops(0, $return_instances);
                }
                else
                {
                    // Can see all the operators
                    $ops = OperatorMan::Instance()->get(null, $return_instances);
                    if (!$op_is_admin) $ops = array_diff_key($ops, OperatorMan::Instance()->get_admins($return_instances));
                }
            }
            else if ($id_owner == 0 || $id_owner == $op_owner_id)
            {
                $ops = OperatorMan::Instance()->get_by_owner($op_owner_id, $return_instances);
            }
            
            if ($ops && $accesses)
            {
                $filtered = array();
                if (!is_array($accesses)) $accesses = array($accesses);
                
                foreach ($accesses as $access)
                {
                    foreach ($ops as $id=>$op)
                    {
                        if (AccessMan::Instance()->has_access($id, $access))
                        {
                            $filtered[$id] = $op;
                        }
                    }
                }
                
                $ops = $filtered;
            }
            
            if ($op_is_admin) 
            {
                $ops += OperatorMan::Instance()->get_admins($return_instances);
            }
        }
        
        return $ops;
    }

    /** Get the owners visible by the current operator */
    public function get_owners($return_instances = true)
    {
        if (!$this->is_blocked())
        {
            if ($this->is_admin($this->m_logged) || $this->m_logged->get_owner_id() == 0)
            {
                // Can see all the operators
                return OwnerMan::Instance()->get(null, $return_instances);
            }
            
            $owner_id = $this->m_logged->get_owner_id();
            if (!$return_instances) return array($owner_id=>$owner_id);
            
            $owner = new Owner($owner_id);
            return array($owner_id=>$owner);
        }
        
        return array();
    }
    
    /** Get the locations visible by the current operator */
    public function get_locations($sel_owner_id = 0, $return_instances = true)
    {
        if (!$this->is_blocked())
        {
            $logged_owner_id = $this->m_logged->get_owner_id();
            
            if ($logged_owner_id != 0)
            {
                if ($sel_owner_id == 0) $sel_owner_id = $logged_owner_id; // Default to logged owner
                else if ($logged_owner_id != $sel_owner_id) return array(); // No access to selected owner
            }
            
            if ($this->can(OperatorAccess::ACCESS_ALL_LOCATIONS))
            {
                // Operator is not limited by the owner, or can access all locations for the owner
                return $sel_owner_id == 0 ? LocationMan::Instance()->get(null, $return_instances) : 
                LocationMan::Instance()->get_by_owner($sel_owner_id, $return_instances);
            }
            
            // Get operator locations
            $zones = ZoneMan::Instance()->get_by_operator($this->m_logged->get_id(), true, false);
            return ZoneMan::Instance()->get_locations($zones, $return_instances);
        }
        
        return array();
    }
    
    /** Get the zones visible by the current operator */
    public function get_zones($sel_owner_id = 0, $return_instances = true)
    {
        if (!$this->is_blocked())
        {
            $logged_owner_id = $this->m_logged->get_owner_id();
            
            if ($logged_owner_id != 0)
            {
                if ($sel_owner_id == 0) $sel_owner_id = $logged_owner_id; // Default to logged owner
                else if ($logged_owner_id != $sel_owner_id) return array(); // No access to selected owner
            }
            
            if ($this->can(OperatorAccess::ACCESS_ALL_LOCATIONS))
            {
                // Operator is not limited by the owner, or can access all locations for the owner
                return $sel_owner_id == 0 ? ZoneMan::Instance()->get(null, $return_instances) :
                ZoneMan::Instance()->get_by_owner($sel_owner_id, $return_instances);
            }
            
            // Get operator zones
            return ZoneMan::Instance()->get_by_operator($this->m_logged->get_id(), true, $return_instances);
        }
        
        return array();
    }
}
?>