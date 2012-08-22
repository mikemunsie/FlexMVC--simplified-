<?php

/**
 * Class for handling files
 *
 * PHP Versions 5.3+
 *
 * flexMVC(tm) : Rapid Development Framework
 * Copyright 2012, Michael Munsie
 *
 * @copyright     Copyright 2012, Michael Munsie, http://mikemunsie.com
 * @link          http://mikemunsie.com
 * @package       flexMVC
 * @since         flexMVC V.1
 */

class File extends \flexMVC{

	private static $show_errors = true;
	private static $validCharsRegex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';
	private static $max_file_upload_size = 0;
	public static $last_file_uploaded = "";		
	public static $last_file_uploaded_ext = "";
	
/**
 * Constructor
 */	
	public static function init(){ 
		File::$max_file_upload_size = File::convert_to_bytes(ini_get('post_max_size'));
	}
	
/**
 * Trigger error routine (overloaded to quickly toggle between visiblity on errors)
 * 
 * @param str $p1
 * @param str $p2
 * @access public
 */		
	public static function trigger_error($p1, $p2){
		if(File::$show_errors) trigger_error($p1, $p2);
	}
	
/**
 * Convert measurement to bytes (Meg or Kilo)
 * 
 * ### Usage:
 *
 * {{{
 * self::$Libs->File->convert_to_bytes("1M"); // Gets the measurement for 1 megabyte
 * self::$Libs->File->convert_to_bytes("1K"); // Gets the measurement for 1 kilobytes
 * }}}
 * 
 * @param str $size
 * @access public
 */	
	private static function convert_to_bytes($size="1M"){
		$unit = substr($size, -1);
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : 1));
		$file_size = (int)substr($size, 0, -1);
		return $file_size * $multiplier;
	}
	
/**
 * Change file upload size (measured in megs or kilobytes)
 *
 * ### Usage:
 *
 * {{{
 * File::change_file_upload_size("1M"); // Changes upload size to 1 megabyte
 * File::change_file_upload_size("1K"); // Changes upload size to 1 kilobyte
 * }}}
 *
 * @return boolean Returns true on success, false on failure
 * @access private
 */		
	private static function change_upload_file_size($size="2M"){
		ini_set("post_max_size", $size);
		ini_set("upload_max_filesize", $size);
		File::$max_file_upload_size = $size;
	}

/**
 * Get the file name of a file (no extension)
 *
 * ### Usage:
 *
 * {{{
 * self::$Libs->File->get_file_name($str)
 * }}}
 *
 * @return str on success, false on failure
 * @access private
 */		
	private static function get_file_name($str){
		if(strpos($str,".")) return substr($str,0,strpos($str,"."));
		else return $str;
	}

/**
 * Upload a file via XHR
 *
 * ### Usage:
 *
 * {{{
 *	$params = array("xhr_name" => self::$get['form_element'],
 * 					"overwrite" => false,
 *					"valid_extensions" => array("jpg","gif","png"),
 *					"form_name" => self::$files['file']['form_name'],
 *					"save_file_name" => "munstro",
 *					"save_path" => "/var/www",
 *					"max_upload_size" => "1M");
 *	self::$Libs->File->upload($params);
 * }}}
 *
 * @return boolean Returns true on success, false on failure
 * @access public
 */	

	public static function upload_xhr($params){
	
		if(!$params) File::trigger_error("Please make sure paramaters have been passed", E_USER_NOTICE);
			
		try{
	
			// Make sure save path has been defined 	
			if(!isset($params['save_path'])){ 
				File::trigger_error("Please define a save path", E_USER_NOTICE); 	
				return false;
			}
			// Append a "/" if the user did not to the end of the save path
			if(substr($params['save_path'],-1) != "/")$params['save_path'].="/";
			
			// Make sure the name of the element was passed
			if(!isset($params['xhr_name'])){ 
				File::trigger_error("Please define the name of xhr get variable for the upload.", E_USER_NOTICE); 	
				return false;
			}		
			
			// Check if the user wanted to specify a maximum upload size
			if(isset($params['max_upload_size'])){
				File::$max_file_upload_size = File::convert_to_bytes($params['max_upload_size']); 
				File::change_upload_file_size(File::$max_file_upload_size);
			}
			
			// Check if the user wants to overwrite a file (if needed)
			if(!isset($params['overwrite'])) $overwrite = false;
			else $overwrite = $params['overwrite'];
			
			// Check if the user wants certain extensions
			if(!isset($params['valid_extensions'])) $valid_extensions = "*";
			else $valid_extensions = $params['valid_extensions'];
			
			// Get the save file name (set the name of the file uploaded as default)
			if(!isset($params['save_file_name'])){
				$pathinfo = pathinfo($params['xhr_name']);
				$params['save_file_name'] = $pathinfo['filename'];
			}	
			 	
			// Grab the permissions for the file
			if(!isset($params['permissions'])) $params['permissions'] = 0777;	
			 	
			// Make sure the content is smaller than the maximum file upload size
			$filesize = (int)$_SERVER["CONTENT_LENGTH"];
			if($filesize > File::$max_file_upload_size){ 
				File::trigger_error("POST exceeded maximum allowed size.", E_USER_NOTICE);
				return false;
			}elseif($filesize <= 0){
				File::trigger_error("File size outside allowed lower bound", E_USER_NOTICE);
				return false;
			}
			
			// Make sure save path exists		
			if(!file_exists($params['save_path'])){
				File::trigger_error("Save Path does not exist", E_USER_NOTICE);
				return false;
			}
			
			// Clean the file name
			$file_name = preg_replace('/[^'.File::$validCharsRegex.']|\.+$/i', "", $params['save_file_name']);
			if(strlen($file_name) == 0){
				File::trigger_error("Invalid file name: " . $params['save_file_name'], E_USER_NOTICE);
				return false;
			}
			
			// Get the file extension
			$path_info = pathinfo($params['xhr_name']);
			if(isset($path_info["extension"])) $file_extension = strtolower($path_info["extension"]);
			else $file_extension = "";
			
			
			// Make sure extension is valid
			if($valid_extensions != "*"){
				if(!in_array($file_extension, $valid_extensions)){
					File::trigger_error("File extension: $file_extension is not valid", E_USER_NOTICE);
					return false;
				}
			}
			
			// Check if the user wants to overwrite the file	
			if(!$overwrite){	
				if(file_exists($params['save_path'] . $file_name . "." . $file_extension)){  
					File::trigger_error("File: " . $params['save_path'] .  $file_name . "." . $file_extension . " already exists", E_USER_NOTICE);
					return false;
				}
			}	
			
			// Upload File
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);
			if($realSize != $filesize) return false;
			$target = fopen($params['save_path'] . $file_name . "." . $file_extension, "w");        
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
			
			// Store the last file uploaded
			File::$last_file_uploaded = $params['save_path'].$params['save_file_name'].".".$file_extension;
			File::$last_file_uploaded_ext = $file_extension;
			
			// Change the permission to that file
			chmod(File::$last_file_uploaded, $params['permissions']);
			return true;

		// Error in uploading file	
		}catch(Error $e){
			File::trigger_error($e, E_USER_NOTICE);
			return false; 
		}				
	}
 
/**
 * Upload a regular file
 *
 * ### Usage:
 *
 * {{{
 *	$params = array("overwrite" => false,
 *					"valid_extensions" => array("jpg","gif","png"),
 *					"form_name" => self::$files['file']['form_name'],
 *					"save_file_name" => "munstro",
 *					"save_path" => "/var/www",
 *					"max_upload_size" => "1M");
 *	self::$Libs->File->upload($params);
 * }}}
 *
 * @return boolean Returns true on success, false on failure
 * @access public
 */	 
	public static function upload($params=false){
	
		if($params['xhr_name']) return File::upload_xhr($params);
		
		// Make sure parameters have been passed
		if(!$params) File::trigger_error("Please make sure paramaters have been passed", E_USER_NOTICE);
			
		try{
	
			// Make sure save path has been defined 	
			if(!isset($params['save_path'])){ 
				File::trigger_error("Please define a save path", E_USER_NOTICE); 	
				return false;
			}
			// Append a "/" if the user did not to the end of the save path
			if(substr($params['save_path'],-1) != "/")$params['save_path'].="/";
			
			// Make sure the name of the element was passed
			if(!isset($params['form_name'])){ 
				File::trigger_error("Please define the name of form element for the upload.", E_USER_NOTICE); 	
				return false;
			}		
			
			// Check if the user wanted to specify a maximum upload size
			if(isset($params['max_upload_size'])) File::$max_file_upload_size = File::convert_to_bytes($params['max_upload_size']); 
			
			// Check if the user wants to overwrite a file (if needed)
			if(!isset($params['overwrite'])) $overwrite = false;
			else $overwrite = $params['overwrite'];
			
			// Check if the user wants certain extensions
			if(!isset($params['valid_extensions'])) $valid_extensions = "*";
			else $valid_extensions = $params['valid_extensions'];
			
			// Get the save file name (set the name of the file uploaded as default)
			if(!isset($params['save_file_name'])){
				$params['save_file_name'] = File::get_file_name(self::$files[$params['form_name']]['name']);
			}	
			 	
			// Grab the permissions for the file
			if(!isset($params['permissions'])) $params['permissions'] = 0777;	
			 	
			// Make sure the content is smaller than the maximum file upload size
			if(self::$files[$params['form_name']]['size'] > File::$max_file_upload_size){ 
				File::trigger_error("POST exceeded maximum allowed size.", E_USER_NOTICE);
				return false;
			}

			// Make sure save path exists		
			if(!file_exists($params['save_path'])){
				if($params['create_directory']){
					File::make_directory(array("base_directory" => false,
											   "path" => $params['save_path']));
				}else{
					File::trigger_error("Save Path does not exist", E_USER_NOTICE);
					return false;
				}
			}
			
			// Define the unique upload error messages
			$uploadErrors = array(
				0 => "There is no error, the file uploaded with success",
				1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
				2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
				3 => "The uploaded file was only partially uploaded",
				4 => "No file was uploaded",
				6 => "Missing a temporary folder"
			);
			

			// Check for errors
			if(!isset($_FILES[$params['form_name']])) {
				File::trigger_error("No upload found in \$_FILES for " . File::$uploadName, E_USER_NOTICE);
				return false;
			} else if(isset($_FILES[$params['form_name']]["error"]) && $_FILES[$params['form_name']]["error"] != 0) {
				File::trigger_error($uploadErrors[$_FILES[File::$uploadName]["error"]], E_USER_NOTICE);
				return false;
			} else if(!isset($_FILES[$params['form_name']]["tmp_name"]) || !@is_uploaded_file($_FILES[$params['form_name']]["tmp_name"])) {
				File::trigger_error("Upload failed is_uploaded_file test.", E_USER_NOTICE);
				return false;
			} else if (!isset($_FILES[$params['form_name']]['name'])) {
				File::trigger_error("File has no name.", E_USER_NOTICE);
				return false;
			}
			
			// Check file size
			$file_size = @filesize($_FILES[$params['form_name']]["tmp_name"]);
			if (!$file_size || $file_size > File::$max_file_upload_size) {
				File::trigger_error("File exceeds the maximum allowed size", E_USER_NOTICE);
				return false;
			}
			if ($file_size <= 0) {
				File::trigger_error("File size outside allowed lower bound", E_USER_NOTICE);
				return false;
			}
			
			// Clean the file name
			$file_name = preg_replace('/[^'.File::$validCharsRegex.']|\.+$/i', "", $params['save_file_name']);
			if(strlen($file_name) == 0){
				File::trigger_error("Invalid file name: " . $params['save_file_name'], E_USER_NOTICE);
				return false;
			}
			
			// Get the file extension
			$path_info = pathinfo($_FILES[$params['form_name']]['name']);
			if(isset($path_info["extension"])) $file_extension = strtolower($path_info["extension"]);
			else $file_extension = "";
			
			// Make sure extension is valid
			if($valid_extensions != "*"){
				if(!in_array($file_extension, $valid_extensions)){
					File::trigger_error("File extension: $file_extension is not valid", E_USER_NOTICE);
					return false;
				}
			}
			
			// Check if the user wants to overwrite the file	
			if(!$overwrite){	
				if(file_exists($params['save_path'] . $file_name . "." . $file_extension)){  
					File::trigger_error("File: " . $params['save_path'] .  $file_name . "." . $file_extension . " already exists", E_USER_NOTICE);
					return false;
				}
			}	
			
			// Try and upload the file
			if(@move_uploaded_file($_FILES[$params['form_name']]["tmp_name"], $params['save_path'].$params['save_file_name'].".".$file_extension)) {
								
				// Store the last file uploaded
				File::$last_file_uploaded = $params['save_path'].$params['save_file_name'].".".$file_extension;
				File::$last_file_uploaded_ext = $file_extension;
				
				// Change the permission to that file
				chmod(File::$last_file_uploaded, $params['permissions']);
				
			}else{	
				File::trigger_error("File could not be saved. Please check permissions.", E_USER_NOTICE);
				return false;
			}

			return true;
		
		// Error in uploading file	
		}catch(Error $e){
			File::trigger_error($e, E_USER_NOTICE);
			return false; 
		}		
	}	
	
/**
 * Make a directory
 * 
 * @return str on success, false on failure
 * @access private
 */		
	public static function make_directory($params=false){
		
		// Make sure params were passed
		if(!$params) return false;
		
		// Make sure all paramaters have been defined
		if(!isset($params['base_directory'])){
			File::trigger_error("Please define a base directory");
			return false;
		}else if(!isset($params['path'])){
			File::trigger_error("Please define a path relative to the base directory to create the new folders");
			return false;
		}else if(!isset($params['permissions'])) $params['permissions'] = 0777;
	
		// Loop through each directory and create (also change permission)
		$dirs = explode("/", $params['path']);
		$save_path = $params['base_directory'];
		foreach($dirs as $dir){
			if(!empty($dir)){
				$save_path.= $dir . "/";
				if(!is_dir($save_path)){
					if(mkdir($save_path, $params['permissions'], false)){ 
						chmod($save_path, $params['permissions']);
					}else return false;
				}
			}
		}
		return true;
	}	
	
/**
 * Remove a file
 * 
 * @return boolean true on success, false on failure
 * @access private
 */		
	public static function remove($file){
		if(file_exists($file)) return unlink($file);
		else return false;
	}
	
/**
 * Copy a file
 * 
 * @return boolean true on success, false on failure
 * @access private
 */			
	public static function copy($file, $location){
		return copy($file, $location);
	}
}
