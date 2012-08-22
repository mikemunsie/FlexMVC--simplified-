<?php

/**
 * Database Tools (Exporting, etc.)
 *
 * Functions for downloading that database
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
 
class DBTools extends \flexMVC{

/**
 * Export the Database
 * 
 * @return void
 * @access public
 */		
	public static function export(){
		$date = date("m-d-Y");
		$filename = $date.".sql";
		$username = \Database::$connections[\Database::$current_connection]['username'];
		$password = \Database::$connections[\Database::$current_connection]['password'];
		$host = \Database::$connections[\Database::$current_connection]['hostspec'];
		$database = \Database::$connections[\Database::$current_connection]['database'];
		$file = APPLICATION."/".$filename;
		$file = str_replace("\\","/",$file);
		$command="mysqldump --host=$host --user=$username --password=$password $database > $file";
		system($command);
		header("Content-type: application/sql");
		header("Content-Disposition: attachment; filename=$filename");
		echo(file_get_contents($file));
		@unlink($file);
		exit;
	}
}