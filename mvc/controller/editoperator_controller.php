<?php
// Copyright 2005-2021 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Edit Operator Controller */
class EditOperatorController extends CController
{
    /** Initialize the form */
    protected function initialize()
    {
        $id = (int)$this->get_arg('id');
        $operator = new Operator($id);
        
        $this->set_arg('first_name', $this->choose_value($operator->get_name(), 'first_name'));
        $this->set_arg('last_name', $this->choose_value($operator->get_last_name(), 'last_name'));
        $this->set_arg('email', $this->choose_value($operator->get_email(), 'email'));
        $this->set_arg('phone', $this->choose_value($operator->get_phone(), 'phone'));
        
        // Set owner fields -------------------------------------------------
        
        $id_owner = $id ? $operator->get_owner_id() : $this->get_arg('id_owner');
        
        if ($id_owner)
        {
            $owner = new Owner($id_owner);
            $this->set_arg('owner_name', $owner->get_name()); // read-only
        }
        
        // Choose timezone --------------------------------------------------
        
        $timezones = array(
        'None'=>'None',
        'America/New_York'=>'America/New_York',
        'America/Chicago'=>'America/Chicago',
        'America/Denver'=>'America/Denver',
        'America/Los_Angeles'=>'America/Los_Angeles',
        'America/Anchorage'=>'America/Anchorage',
        'UTC'=>'Universal Time');
        
        $timezone = $this->choose_value($operator->get_timezone(), 'timezone');
        if (!$timezone) $timezone = 'None';
        
        $this->set_arg('timezone', $timezone);
        $this->set_arg('timezones', $timezones);
        
        // Access levels ----------------------------------------------------
        
        static $levels = null;
        if ($levels === null) $levels = LoggableAccess::Instance()->get_access_levels();
        
        if ($levels)
        {
            $level = $this->choose_value(LoggableAccess::Instance()->get_access_level($operator), 'level');
            if (!$level) $level = 1;
            
            // The operator can't change his own level
            $avail_levels = ($id == LoggableAccess::Instance()->get_logged()->get_id()) ? 
            array($level=>$levels[$level]) : $levels;
            
            $this->set_arg('level', $level);
            $this->set_arg('levels', $avail_levels);
        }
        
        $this->enable_post(true);
    }
    
    /** Process the form */
    protected function process()
    {
        $id = (int)$this->get_value('id');
        $is_new = $id == 0;
        
        $first_name = $this->get_value('first_name');
        $last_name = $this->get_value('last_name');
        $email = $this->get_value('email');
        $phone = $this->get_value('phone');
        $login = $this->get_value('email');
        $timezone = $this->get_value('timezone');
        $level = $this->get_value('level');
        
        $id_owner = $this->get_value('id_owner');
        $id_access = $this->get_value('id_access');
        
        $label = Operator::label();
        $validator = new UserFieldValidator();
        
        if (!$validator->is_name($first_name, 'First name'))
        {
            CHtmlForm::set_error($validator->get_error('First name'), 'first_name');
        }
        
        if (!$validator->is_name($last_name, 'Last name'))
        {
            CHtmlForm::set_error($validator->get_error('Last name'), 'last_name');
        }
        
        if (!$validator->is_email($email, 'Email'))
        {
            CHtmlForm::set_error($validator->get_error('Email'), 'email');
        }
        
        if ($phone && !$validator->is_phone($phone, 'Phone'))
        {
            CHtmlForm::set_error($validator->get_error('Phone'), 'phone');
        }
        
        if (!$validator->is_login($login, 'Login'))
        {
            CHtmlForm::set_error($validator->get_error('Login'), 'login');
        }
        else if (!$validator->is_login_available($login, 'operators', $id, 'login', 'Login'))
        {
            CHtmlForm::set_error($validator->get_error('Login'), 'login');
        }
        
        $errors = CHtmlForm::get_errors();
        
        if ($errors)
        {
            CHtmlPage::set_last_error(implode('<br/>', $errors));
            return false;
        }
        
        // Update operator --------------------------------------------------
        
        $operator = new Operator($id);
        $operator->set_name($first_name);
        $operator->set_last_name($last_name);
        $operator->set_email($email);
        $operator->set_phone(UserFieldValidator::normalize_phone($phone));
        $operator->set_login($login);
        $operator->set_timezone($timezone == 'None' ? '' : $timezone);
        
        if ($is_new)
        {
            // Generate password
            $password = OperatorMan::Instance()->generate_password();
            $operator->set_password($password);
        }

        if (!ServerAdmin::Instance()->edit_operator($operator))
        {
            $action = $is_new ? 'add' : 'update';
            CHtmlPage::set_last_error("Failed to {$action} {$label} {$first_name} {$last_name}");
            return false;
        }

        if ($level && !LoggableAccess::Instance()->set_access_level($operator, $level))
        {
            CHtmlPage::set_last_error("Failed to set admin level for {$label} {$first_name} {$last_name}");
            return false;
        }
        
        if ($is_new) // Set owner and location access
        {
            if ($id_owner == 0) 
            {
                // Default to logged operator's owner
                $id_owner = LoggableAccess::Instance()->get_logged()->get_owner_id();
            }
            
            if ($id_owner != 0) 
            {
                $operator->set_owner_id($id_owner, true);
                
                if (!AccessMan::Instance()->has_access($operator->get_id(), OperatorAccess::ACCESS_ALL_LOCATIONS))
                {
                    $id_zone = $this->get_value('id_zone');
                    
                    if ($id_zone != 0)
                    {
                        ZoneMan::Instance()->add_operators($id_zone, $operator);
                    }
                }
            }
                        
            if ($id_access != 0)
            {
                AccessMan::Instance()->add_access($operator->get_id(), $id_access);
            }
        }
        
        $action = $is_new ? 'added' : 'updated';
        $info = "{$label} {$first_name} {$last_name} was {$action}";
        if ($level || $id_access) $info .= "<br/>Default access rights were added to the new {$label}";
        
        CHtmlPage::set_last_info($info);
        
        return true;
    }
}
?>