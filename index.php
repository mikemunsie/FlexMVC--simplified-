<?php

/**
 * Set route to FlexMVC
 *
 * PHP Versions 5.3+
 *
 * flexMVC(tm) : Rapid Development Framework
 * Copyright 2012, Michael Munsie
 *
 * @copyright     Copyright 2012, Michael Munsie, http://mikemunsie.com
 * @link          http://mikemunsie.com
 * @package       flexMVC
 * @version       flexMVC V.1
 */ 
 
// Auto detect directory
$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'],"/");
$dir = str_replace($_SERVER['DOCUMENT_ROOT'],"",$_SERVER['SCRIPT_FILENAME']);
$dir = substr($dir, 0, strrpos($dir,"/"));

// Define Paths
define('DEFAULT_DIR', $dir);
define('DOCUMENT_ROOT', realpath(dirname(__FILE__))."/");

// Define where the framework is located
define('APPLICATION', DOCUMENT_ROOT."application/");

// Include Config Class
require_once(APPLICATION."libs/flexmvc.php");

// Run flexMVC Web App
flexMVC::init_web_app(); 