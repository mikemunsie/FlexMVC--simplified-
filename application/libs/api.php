<?php

class API extends flexMVC{

	public static $success = false;		// Holds Ajax Success
	public static $results = false;		// Holds Ajax Results
	public static $error = false;		// Holds Ajax Error
	public static $return_ajax = true;	// Return as AJAX (auto-detects Ajax request)
	
/**
 * Send Return
 *
 * @return mixed array
 * @access public
 */		
	public static function send_return(){
		$return = array("success" => API::$success,
						"results" => API::$results,
						"error" => API::$error);
		if(IS_AJAXREQUEST && API::$return_ajax){
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");	
			header("Content-Type: APPLICATION/json");	
			echo(json_encode($return));
			exit;
		}else{
			return $return;
		}
	}	
}