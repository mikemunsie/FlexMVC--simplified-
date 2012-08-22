<?php

/**
 * Zip Class
 *
 * Functions for compressing that zip folder
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
 
class Zip extends \flexMVC{
	
/**
 * Recursive Zip a Folder
 * 
 * @return void
 * @access public
 */		
	public static function recurse_zip($src, &$zip, $path_length){
		$dir = opendir($src);
		while(false !== ($file = readdir($dir))){
			if(( $file != '.' ) && ( $file != '..' )){
				if(is_dir($src . '/' . $file)){
					self::recurse_zip($src . '/' . $file,$zip,$path_length);
				}
				else{
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path_length));
				}
			}
		}
		closedir($dir);
	}
	
/**
 * Compress a Folder
 * 
 * @return void
 * @access public
 */			
	public static function compress($src, $save_as_file=false, $destination=false, $filename="backup"){
		
		// Create the destination and savename
		if($save_as_file){
			if(!$destination) die("Please enter a destination");
			$filename = $destination."/".$filename.".zip";
		}
		
		// Begin the Compressing Process
		$src = str_replace("\\","/",$src);
		if(substr($src,-1)==='/') $src = substr($src,0,-1);
		$arr_src=explode('/',$src);
		unset($arr_src[count($arr_src)-1]);
		$path_length=strlen(implode('/',$arr_src).'/');
		$zip = new \ZipArchive;
		$res = $zip->open($filename, \ZipArchive::CREATE);
		if($res !== TRUE){
			die('Error: Unable to create zip file');
		}
		if(is_file($src)) $zip->addFile($src,substr($src,$path_length));
		else{
		if(!is_dir($src)){
			 $zip->close();
			 @unlink($filename);
			 echo 'Error: File not found';
			 exit;
		}
		self::recurse_zip($src,$zip,$path_length);}
		$zip->close();
		
		// Output the zip file if we decide to humbly do so
		if(!$save_as_file){
			$date = date("m-d-Y");
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename=$date.zip");
			echo(file_get_contents($filename));
			@unlink($filename);
			exit;
		}
		return true;
	}
} 
