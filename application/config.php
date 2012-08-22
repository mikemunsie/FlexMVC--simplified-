<?php
 
// Define if the site is HTTPS only
define('HTTPS_ONLY', false);
 
// Display Errors
define('DISPLAY_ERRORS', 'YES');
define('LOG_ERRORS', 'YES');

// Default Layout
define('DEFAULT_LAYOUT', "default");

// Database settings
define('DEFAULT_DBCONNECTION', 'local');

// Extensions
define('PHP_EXT', '.php');
define('VIEWPAGE_EXT', ".php");

// Default security Salt 
define('SALT', '!a!33@13!sZc');

// Define Database Connections
$_ENV['database'] = array("local" =>  array("username" => "root",
										    "password" => "",
										    "hostspec" => "localhost",
										    "database" => "wordpress",
										    "model" => "local",
										    "driver" => "mysql"));									   