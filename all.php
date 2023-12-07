<?php
define('SELECTOR_OWNER_ID', 'sel_id_owner');
define('SELECTOR_LOCATION_ID', 'sel_id_location');
define('SELECTOR_ZONE_ID', 'sel_id_zone');
define('SELECTOR_ACCESS_ID', 'sel_id_access');

class UserDb
{
    private static $name = null;
    public static function get_name(){ return self::$name; }
    public static function set_name($name){ self::$name = $name; }
}

$__dir = dirname(__FILE__);
require_once($__dir.'/updatable.php');
require_once($__dir.'/blockable.php');
require_once($__dir.'/loggable.php');
require_once($__dir.'/location.php');
require_once($__dir.'/location_man.php');
require_once($__dir.'/contact.php');
require_once($__dir.'/contact_man.php');
require_once($__dir.'/owner.php');
require_once($__dir.'/owner_man.php');
require_once($__dir.'/zone.php');
require_once($__dir.'/zone_man.php');
require_once($__dir.'/operator.php');
require_once($__dir.'/operator_man.php');
require_once($__dir.'/operator_group.php');
require_once($__dir.'/operator_group_man.php');

// Setup JIT class autoload
$__sudir = $__dir.'/extra';
$__classes = array(
    'UserFieldValidator'=>$__sudir.'/user_field_validator.php',
    'UserMsgHelper'=>$__sudir.'/user_msg_helper.php',
);
require_once($__dir.'/../db/extra/c_autoloader.php');
CAutoloader::register($__classes);

$__sudir = $__dir.'/access';
$__classes = array(
    'AccessMan'=>$__sudir.'/access_man.php',
    'LoggableAccess'=>$__sudir.'/loggable_access.php',
    'OperatorAccess'=>$__sudir.'/operator_access.php',
    'ServerAdmin'=>$__sudir.'/server_admin.php',
);
CAutoloader::register($__classes);

$__sudir = $__dir.'/mvc/view';
$__classes = array(
    'AssignLocationsCtrl' => $__sudir . '/assign_locations_ctrl.php',
    'AssignOperatorsCtrl' => $__sudir . '/assign_operators_ctrl.php',
    'AssignZonesCtrl' => $__sudir . '/assign_zones_ctrl.php',
);
CAutoloader::register($__classes);
?>