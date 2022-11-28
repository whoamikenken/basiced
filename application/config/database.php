<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$db_files = $environment = "";
if($_SERVER["HTTP_HOST"] == "192.168.2.97"){
	$db_files = "basicedhrisfiles";
	$environment = "Development";
}else if($_SERVER["HTTP_HOST"] == "hris-demo.pinnacle.edu.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false){
	$db_files = "basicedhris";
	$environment = "Training";
}else if($_SERVER["HTTP_HOST"] == "hris-demo.pinnacle.edu.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false){
	$db_files = "basicedhrisfiles"; 
	$environment = "Production";
} 

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'admin';
$db['default']['password'] = 'passwrd';
$db['default']['database_files'] = $db_files;
$db['default']['database'] = 'basicedhris';
$db['default']['dtr_file'] = 'basicedhrisfiles';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

// $db['default']['GDriveFolder'] = '1flGF8VV2PRLRerPEwQCmzF9lpTdXq0pR';
// $db['default']['refresh_token'] = '1//0eitNuAg1cuaJCgYIARAAGA4SNwF-L9Irf2679EOhYNgYdmW5vWNmm6iSbE6O7h4ulaq-8m8olagB2gS-i9CPcbY-lVGKGZUJEIo';
$db['default']['faceServer'] = 'http://43.255.106.203:8190/';
// $db['default']['tablebackup'] = 'employee,user_info,user_access,leave_app_base,leave_app_tito,leave_app_ti_to,leave_app,leave_request,employee_leave_credit,employee_leave_credit_history,overtime_app,overtime_request,timesheet,timesheet_trail,timesheet_student,timesheet_trail_student,facial_log,ob_app,ob_app_emplist,ob_request,overtime_request,ot_app,ot_app_emplist,login_attempts,login_attempts_terminal,webcheckin_history,webcheckin_trail,timesheet_weblogin';

/* End of file database.php */
/* Location: ./application/config/database.php */
