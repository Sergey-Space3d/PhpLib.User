<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Loggable Access. Manages access of logged operator/user */
abstract class LoggableAccess
{
    const ACCESS_LEVEL_ADMIN = 0xFF;
    const ACCESS_LEVEL_MASK = 0xFF;
    
    /** Loggable instance */
    protected $m_logged = null;
    
    /** Last error */
    private $m_error = null;
    
    // Setup ----------------------------------------------------------------
    
    /** Don't allow cloning */
    final private function __clone() {}
    
    /** Get the instance of the class */
    final public static function Instance($class = null)
    {
        static $instance = null;
        
        if ($instance == null)
        {
            $instance = $class ? new $class : new static();
            
            if ($_POST['__logas__'] || $_GET['__logas__']) 
            {
                $_SESSION['__logas__'] = true;
                $instance->reset_session(null, false);
            }
            
            if ($_SESSION['__loggedclass__'])
            {
                $instance->m_logged = new $_SESSION['__loggedclass__']($_SESSION['__loggedid__']);
                
                if (($instance->m_logged instanceof Loggable) && !$_SESSION['__logas__'])
                {
                    // Update last login time and IP
                    $instance->m_logged->set_ts_logged(time(), true);
                    $instance->m_logged->set_ip_last($_SERVER['REMOTE_ADDR'], true);
                    
                    if (!$instance->m_logged->get_ip_first())
                    {
                        // Set first IP and referer
                        $instance->m_logged->set_ip_first($_SERVER['REMOTE_ADDR'], true);
                        
                        // Extract the host name
                        $arr = @parse_url($_SERVER['HTTP_REFERER']);
                        
                        if ($arr['host']) 
                        {
                            $normalize_host = function($host)
                            {
                                $host = strtolower($host);
                                return substr($host, 0, 4) == 'www.' ? substr($host, 4) : $host;
                            };
                            
                            // Save the referer, if not self-referenced
                            $host = $normalize_host($arr['host']);
                            $sname = $normalize_host($_SERVER['SERVER_NAME']);
                            
                            if ($host != $sname) 
                            {
                                $host .= strtolower($arr['path']); // Remember path (starts with /)
                                $instance->m_logged->set_referer($host, true);
                            }
                        }
                    }
                }
            }
        }
        
        return $instance;
    }
    
    /** The constructor */
    protected function __construct()
    {
    }
    
    /** Reset session values */
    protected function reset_session($logged, $destroy)
    {
        unset($_SESSION['main_page']);
        if ($destroy) @session_destroy();
        @session_regenerate_id();

        $_SESSION['__loggedclass__'] = $logged ? get_class($logged) : null;
        $_SESSION['__loggedid__'] = $logged ? $logged->get_id() : null;
        
        $this->m_logged = $logged;
        
        if (($logged instanceof Loggable) && !$_SESSION['__logas__'])
        {
            // Update last login time
            $logged->set_ts_logged(time(), true);
        }
    }
    
    // Logging --------------------------------------------------------------
    
    /** Login */
    abstract public function login($login, $password, $access = null);
    
    /** Don't allow loggable with different access to be logged */
    abstract public function verify_access($access);
    
    /** Recover by email or phone. Returns loggable instance on success, null otherwise */
    abstract public function recover($email_or_phone);
    
    /** Logoff */
    public function logoff()
    {
        $this->reset_session(null, true);
        return true;
    }
    
    /** Log as somebody else */
    public function log_as($somebody) { $_SESSION['__logas__'] = $somebody; }
    
    /** Inquire if the logged as somebody else */
    public function is_logged_as() { return $_SESSION['__logas__']; }
    
    /** Inquire if the loggable is blocked */
    public function is_blocked()
    {
        return $this->m_logged && $this->m_logged->is_blocked();
    }
    
    /** Inquire if the loggable is top admin */
    protected function is_admin($loggable)
    {
        return ($loggable instanceof Operator) && $loggable->is_admin();
    }
    
    /** Inquire if top admin is logged */
    public function is_logged_admin()
    {
        return $this->is_admin($this->m_logged);
    }
    
    /** Inquire if the loggable is logged */
    public function is_logged()
    {
        return $this->m_logged != null;
    }
    
    /** Get current loggable */
    public function get_logged()
    {
        return $this->m_logged;
    }
    
    /** Get loggable's timezone */
    public function get_timezone()
    {
        if (!$_SESSION['__loggedtimezone__'])
        {
            if ($this->m_logged instanceof Contact)
            {
                if ($this->m_logged->get_timezone())
                {
                    // The contact is assigned the timezone
                    $_SESSION['__loggedtimezone__'] = $this->m_logged->get_timezone();
                }
                else if ($this->m_logged->get_location_id())
                {
                    // Get timezone by location
                    $location = new Location($this->m_logged->get_location_id());
                    $_SESSION['__loggedtimezone__'] = TimeZone::get_timezone_by_location(
                        $location->get_country(), $location->get_state(), $location->get_city());
                }
            }
            
            if (!$_SESSION['__loggedtimezone__'])
            {
                // Get timezone by IP
                $_SESSION['__loggedtimezone__'] = TimeZone::get_timezone_by_ip($_SERVER['REMOTE_ADDR']);
            }
            
            if (!$_SESSION['__loggedtimezone__'])
            {
                // Set UTC as default
                $_SESSION['__loggedtimezone__'] = TimeZone::$DefTimezone;
            }
        }
        
        return $_SESSION['__loggedtimezone__'];
    }
    
    // Access level ---------------------------------------------------------
    
    /** Get access array by level */
    protected function get_access($level)
    {
        // Override
        return array();
    }
    
    /** Get all access levels */
    protected function get_all_levels()
    {
        // Override
        return array();
    }
    
    /** Get access levels, where key is level's number, and value is level's name */
    public function get_access_levels()
    {
        $levels = array();
        $all_levels = $this->get_all_levels();
        
        if ($all_levels)
        {
            $level = $this->get_access_level($this->get_logged());
            
            foreach ($all_levels as $key=>$val)
            {
                if ($key > $level) break;
                $levels[$key] = $all_levels[$key];
            }
        }
        
        return $levels;
    }
    
    /** Get level's name */
    public function get_level_name($level)
    {
        if (!$level) return 'None';
        return $level == LoggableAccess::ACCESS_LEVEL_ADMIN ? 'Server Administrator' : "Admin Level {$level}";
    }
    
    /** Get loggable's access level */
    public function get_access_level(Loggable $loggable)
    {
        $is_admin = ($loggable instanceof Operator) && $loggable->is_admin();
        $level = $is_admin ? LoggableAccess::ACCESS_LEVEL_ADMIN : ($loggable->get_flags() & LoggableAccess::ACCESS_LEVEL_MASK);
        return $level;
    }
    
    /** Set access level. Returns true on success */
    public function set_access_level(Loggable $loggable, $level)
    {
        $loggable->set_flag(LoggableAccess::ACCESS_LEVEL_MASK, false);
        $loggable->set_flag($level, true, true);
        return true;
    }
    
    // Error ----------------------------------------------------------------
    
    /** Get last error */
    public function get_last_error() { return $this->m_error; }
    
    /** Set last error */
    protected function set_last_error($error) { $this->m_error = $error; }
}
?>