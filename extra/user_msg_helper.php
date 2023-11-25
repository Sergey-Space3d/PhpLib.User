<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

class UserMsgHelper extends TemplateMessenger
{
    const MC_RECOVERY_CODE = 'Recovery Code Message';
    const MC_OPERATOR_LOGIN = 'Operator Login Message';
    
    /** Initialize template fields */
    protected function init_template_fields()
    {
        $func = function ($target, $arg)
        {
            $location = new Location($arg->get_location_id());
            return $location->get_full_address(false, false);
        };
        MessageTemplateMan::Instance()->set_field('CONTACT_ADDRESS', $func);
        
        $func = function ($target, $arg)
        {
            // SMS filters out messages with URL
            if ($target instanceof SmsContent) return '';
            
            $ledger_id = AccessMan::Instance()->get_ledger_by_name(OperatorAccess::ADMIN_PANEL);
            $ledger = AccessMan::Instance()->get_ledger($ledger_id);
            
            return $ledger ? $ledger->get_desc() : '';
        };
        MessageTemplateMan::Instance()->set_field('OPERATOR_PORTAL', $func);
        
        $func = function ($target, $arg)
        {
            // SMS filters out messages with email addresses
            if ($target instanceof SmsContent) return '[email]';
            
            return $arg->get_email();
        };
        MessageTemplateMan::Instance()->set_field('CONTACT_EMAIL', $func);
        
        $func = function ($target, $arg)
        {
            // SMS filters out messages with email addresses
            if ($target instanceof SmsContent && $arg->get_email() == $arg->get_login())
            {
                return '[email]';
            }
            
            return $arg->get_login();
        };
        MessageTemplateMan::Instance()->set_field('CONTACT_LOGIN', $func);
        
        $func = function ($target, $arg) { return $arg; };
        MessageTemplateMan::Instance()->set_field('CONTACT_FIRST_NAME', $func);
        MessageTemplateMan::Instance()->set_field('CONTACT_LAST_NAME', $func);
        MessageTemplateMan::Instance()->set_field('CONTACT_PHONE', $func);
        MessageTemplateMan::Instance()->set_field('CONTACT_PASSWORD', $func);
        MessageTemplateMan::Instance()->set_field('RECOVERY_CODE', $func);
    }
    
    /** Make template arglist */
    protected function make_template_args($obj, array $extra = null)
    {
        $args = array();
        $objs = is_array($obj) ? $obj : array($obj);
        
        foreach ($objs as $obj)
        {
            if ($obj instanceof Contact)
            {
                $args['CONTACT_ADDRESS'] = $obj;
                $args['CONTACT_EMAIL'] = $obj;
                $args['CONTACT_LOGIN'] = $obj;
                
                $args['CONTACT_FIRST_NAME'] = $obj->get_name();
                $args['CONTACT_LAST_NAME'] = $obj->get_last_name();
                $args['CONTACT_PHONE'] = $obj->get_phone();
                $args['CONTACT_PASSWORD'] = $obj->get_password();
            }
        }
        
        if ($extra)
        {
            $args = array_merge($args, $extra);
        }
        
        $args['OPERATOR_PORTAL'] = '';
        return $args;
    }
    
    /** Make sample template arglist */
    public function make_sample_template_args(array $extra = null)
    {
        $args = array();
        
        $items = OperatorMan::Instance()->get();
        
        if ($items)
        {
            $item = current($items);
            $args = array_merge($args, $this->make_template_args($item, $extra));
        }
        
        $args['RECOVERY_CODE'] = '1234';
        return $args;
    }
    
    /** Send recovery code. Returns message body on success, false otherwise */
    public function send_recovery_code(Contact $contact, $code)
    {
        $field_args = $this->make_template_args($contact, array('RECOVERY_CODE'=>$code));
        return $this->send($contact->get_fullname(), $contact->get_email(), $contact->get_phone(),
            UserMsgHelper::MC_RECOVERY_CODE, $field_args);
    }
    
    /** Send operator's login info. Returns message body success, false otherwise */
    public function send_operator_login(Operator $operator)
    {
        if ($operator->is_admin() || AccessMan::Instance()->has_access($operator->get_id(), OperatorAccess::ADMIN_PANEL))
        {
            $field_args = $this->make_template_args($operator);
            return $this->send($operator->get_fullname(), $operator->get_email(), $operator->get_phone(),
                UserMsgHelper::MC_OPERATOR_LOGIN, $field_args);
        }
        
        return false;
    }
}
?>