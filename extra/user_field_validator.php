<?php
// Copyright 2005-2022 Sergey Shustov.
// License: GPL v2.  See License.txt.

class UserFieldValidator
{
    private $m_errors = array();
    
    public $MinName = 2;
    public $MaxName = 32;
    
    public $MinLogin = 2;
    public $MaxLogin = 32;
    
    public $MinPassword = 7;
    public $MaxPassword = 32;
    
    /** The the validation errors */
    public function get_errors() { return $this->m_errors; }
    
    /** Get named error */
    public function get_error($field_name) 
    { 
        return isset($this->m_errors[$field_name]) ? $this->m_errors[$field_name] : null; 
    }
    
    /** Returns true if the name is valid */
    public function is_name($name, $field_name = null)
    {
        $len = strlen($name);
        
        if ($len < $this->MinName || $len > $this->MaxName)
        {
            if (!$field_name) $field_name = 'Name';
            $this->m_errors[$field_name] = "{$field_name} should be between {$this->MinName} and {$this->MaxName} characters";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the login is valid */
    public function is_login($login, $field_name = null)
    {
        $len = strlen($login);
        
        if ($len < $this->MinLogin || $len > $this->MaxLogin)
        {
            if (!$field_name) $field_name = 'Login';
            $this->m_errors[$field_name] = "Login should be between {$this->MinLogin} and {$this->MaxLogin} characters";
            return false;
        }
        
        if (preg_match("/\\s/", $login)) 
        {
            if (!$field_name) $field_name = 'Login';
            $this->m_errors[$field_name] = "Whitespace is not allowed in login";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the login is available */
    public function is_login_available($login, $table_name, $id = 0, $input_name = null, $field_name = null)
    {
        $login = strtolower($login);
        $table = new CDbTable(UserDb::get_name().'.'.$table_name);
        if (!$input_name) $input_name = 'login';
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
            
            if (!$field_name) $field_name = 'Login';
            $this->m_errors[$field_name] = "{$field_name} '{$login}' is already used";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the password is valid */
    public function is_password($password1, $password2 = null, $field_name = null)
    {
        if ($password2 !== null && $password1 != $password2)
        {
            if (!$field_name) $field_name = 'Password';
            $this->m_errors[$field_name] = "{$field_name} mismatch";
            return false;
        }
        
        $len = strlen($password1);
        
        if ($len < $this->MinPassword || $len > $this->MaxPassword)
        {
            if (!$field_name) $field_name = 'Password';
            $this->m_errors[$field_name] = "{$field_name} should be between {$this->MinPassword} and {$this->MaxPassword} characters";
            return false;
        }
        
        if (preg_match("/\\s/", $password1))
        {
            if (!$field_name) $field_name = 'Password';
            $this->m_errors[$field_name] = "Whitespace is not allowed in password";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the email is valid */
    public function is_email($email, $field_name = null)
    {
        $email = trim(strtolower($email));
        $n_chars = strlen($email);
        
        if ($n_chars < 6 || // consider minimum 6 characters (a@b.tv)
            ctype_alpha($email[0]) == false || // first char to be alpha
            ctype_alnum($email[$n_chars - 1]) == false) // last char to be alphanum
        {
            if (!$field_name) $field_name = 'Email address';
            $this->m_errors[$field_name] = "{$field_name} is invalid";
            return false;
        }
            
        // Check the pattern
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i';
        
        if (preg_match($regex, $email) == false)
        {
            if (!$field_name) $field_name = 'Email address';
            $this->m_errors[$field_name] = "{$field_name} is invalid";
            return false;
        }
        
        return true;
    }
    
    /** Returns true if the phone is valid (US numbers only) */
    public function is_phone($phone, $field_name = null)
    {
        // Eliminate every char except 0-9
        $justNums = preg_replace("/[^0-9]/", '', $phone);
        
        // Eliminate leading 1 if its there
        if (strlen($justNums) == 11) 
        {
            $justNums = preg_replace("/^1/", '', $justNums);
        }
        
        // If we have 10 digits left, it's probably valid.
        if (strlen($justNums) != 10)
        {
            if (!$field_name) $field_name = 'US phone number';
            $this->m_errors[$field_name] = "{$field_name} should be 10 digits";
            return false;
        }
        
        return true;
    }
    
    /** Normalize phone string */
    public static function normalize_phone($phone)
    {
        // Eliminate every char except 0-9
        $phone = preg_replace("/[^0-9]/", '', $phone);
        
        // Eliminate leading 1 if its there
        if (strlen($phone) == 11)
        {
            $phone = preg_replace("/^1/", '', $phone);
        }
        
        // If we have 10 digits left, it's probably valid.
        if (strlen($phone) == 10)
        {
            $phone = '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, -4);
        }
            
        return $phone;
    }
}
?>