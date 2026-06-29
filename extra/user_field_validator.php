<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class UserFieldValidator extends FieldValidator
{
    public $MinName = 2;
    public $MaxName = 32;
    
    public $MinLogin = 2;
    public $MaxLogin = 32;
    
    /** Returns true if the name is valid */
    public function is_name($name, $field_name = 'Name')
    {
        $len = strlen($name);
        
        if ($len < $this->MinName || $len > $this->MaxName)
        {
            $this->m_errors[$field_name] = "{$field_name} should be between {$this->MinName} and {$this->MaxName} characters";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the login is valid */
    public function is_login($login, $field_name = 'Login')
    {
        $len = strlen($login);
        
        if ($len < $this->MinLogin || $len > $this->MaxLogin)
        {
            $this->m_errors[$field_name] = "Login should be between {$this->MinLogin} and {$this->MaxLogin} characters";
            return false;
        }
        
        if (preg_match("/\\s/", $login)) 
        {
            $this->m_errors[$field_name] = "Whitespace is not allowed in login";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the login is available */
    public function is_login_available($login, $table_name, $id = 0, $input_name = 'login', $field_name = 'Login')
    {
        $login = strtolower($login);
        $table = new CDbTable(UserDb::get_name().'.'.$table_name);
        $fields = $table->get_fields("{$input_name}='{$login}'");
        
        if ($fields)
        {
            if ($id != 0)
            {
                foreach ($fields as $field)
                {
                    if ($field['id'] == $id)
                    {
                        return true;
                    }
                }
            }
            
            $this->m_errors[$field_name] = "{$field_name} '{$login}' is already used";
            return false;
        }
        
        return true;
    }
}
?>