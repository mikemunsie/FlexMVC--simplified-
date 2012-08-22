<?php 

/**
 * Authorization Class
 *
 * Functions for authorizing webpages
 *
 * PHP versions 5
 *
 * munsieMVC(tm) : Rapid Development Framework
 * Copyright 2011-2012, Michael Munsie
 *
 * @copyright     Copyright 2011-2012, Michael Munsie, http://mikemunsie.com
 * @link          http://mikemunsie.com
 * @package       munsieMVC
 * @version       munsieMVC V.4
 */
 
class Auth extends \flexMVC{
	
/**
 * Hash a string
 * 
 * @param str $str
 * @return md5(SALT.$str)
 * @access public
 */		
	public static function hash($str){
		return md5(SALT.$str);
	}
	
/**
 * Get IP Address
 *
 * @return string $ip
 * @access public
 */			
	public static function get_ip() {  
		if (getenv("HTTP_CLIENT_IP")  && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))  
		$ip = getenv("HTTP_CLIENT_IP");  
		else if (getenv("REMOTE_ADDR")  && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))  
		$ip = getenv("REMOTE_ADDR");  
		else if (getenv("HTTP_X_FORWARDED_FOR")  && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))  
		$ip = getenv("HTTP_X_FORWARDED_FOR");  
		else if (isset($_SERVER['REMOTE_ADDR'])  && $_SERVER['REMOTE_ADDR']  && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))  
		$ip = $_SERVER['REMOTE_ADDR'];  
		else {  $ip = "unknown";  return $ip;  } 
		return $ip;
	}	
	
/**
 * Check a session public staticiable with a value
 *
 * @param string $session_str
 * @param string $value
 * @return string $check
 * @access public
 */		
	public static function session_check($session_str, $value='') {
		$check = true;
		if (isset($_SESSION[$session_str]) != true || $_SESSION[$session_str] != "$value") $check=false;   
		return $check;
	}
 
/**
 * Destroy a session
 *
 * @return void 
 * @access public
 */		
	public static function end_session() { session_destroy(); }

/**
 * Restricted access for a page
 *
 * Makes a page unable to access without password or username.
 * @param string $user
 * @param string $password
 * @param string $key_name
 * @return void
 * @access public
 */	
	public static function restricted_access($user, $password, $key_name="default", $hash_password=false) {
		
		// Check if session contains controllers
		if(isset($_SESSION['restricted']['access']['key'])){
			if(in_array($key_name, $_SESSION['restricted']['access']['key'])) return true;
		}		
		
		// Check if a request has been sent
		if(isset($_REQUEST)){

			// Make sure post data was sent
			if(isset($_REQUEST['user']) && isset($_REQUEST['password'])){
				
				if($hash_password) $_REQUEST['password'] = \Libs\Auth::hash($_REQUEST['password']);
				
				// Check the user and password
				if($_REQUEST['user'] == $user && $_REQUEST['password'] == $password){
					
					// Get all the controllers and add the current controller to the array
					if(isset($_SESSION['restricted']['access']['key'])){
						$controllers = $_SESSION['restricted']['access']['key'];
						array_push($controllers, $key_name);					
						$_SESSION['restricted']['access']['key'] = $controllers;
						
					// This is the first controller added to the session
					}else $_SESSION['restricted']['access']['key'] = array($key_name);
					return true;
				}
			}
		}
		
		Header("HTTP/1.1 401 Unauthorized");
		\flexMVC::$layout = "restricted_access";
		\flexMVC::private_method('\flexMVC::render');
		exit;
	}	
	
/**
  Check if a user has the restricted access key
 *
 * @param str $key
 * @return void
 * @access public
 */		
	public static function has_restricted_key($key){
		if(isset($_SESSION['restricted']['access']['key'])){
			return in_array($key, $_SESSION['restricted']['access']['key']);
		}
		return false;
	}
} 
