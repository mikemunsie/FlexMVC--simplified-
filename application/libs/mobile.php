<?php

class Mobile extends \flexMVC{

/**
 * Simple routine that simply says am I a mobile user
 *
 * @return boolean true IF mobile, false IF not mobile
 * @access public
 */
	public static function detect(){
		
		// Get user agent
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		// Check the most popular mobile browsers
		if(stripos($user_agent, "iphone") ||
		   stripos($user_agent, "ipod") ||
		   stripos($user_agent, "ipad") ||
		   stripos($user_agent, "android") ||
		   stripos($user_agent, "opera mini") ||
		   stripos($user_agent, "mobile")) return true;
		
		// Return false if user is not mobile
		return false;	
	}
	
/**
 * Simple routine that simply says am I a mobile user
 *
 * @return boolean true IF mobile, false IF not mobile
 * @access public
 */
	public static function detect_mobile(){
		
		// Get user agent
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		// Check the most popular mobile browsers
		if(stripos($user_agent, "iphone") ||
		   stripos($user_agent, "ipod") ||
		   stripos($user_agent, "android") ||
		   stripos($user_agent, "opera mini") ||
		   stripos($user_agent, "mobile")) return true;
		
		// Return false if user is not mobile
		return false;	
	}	

/**
 * Simple routine that simply says am I a tablet user
 *
 * @return boolean true IF mobile, false IF not mobile
 * @access public
 */	
	public static function detect_ipad(){
		if(stripos($user_agent, "ipad")) return true;
		return false;
	}
}
