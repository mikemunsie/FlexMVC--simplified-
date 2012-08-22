<?php

/**
 * flexMVC Class
 *
 * Functions for controllers, content, layouts, and getting URL information
 * This version has been modified to secure code
 *
 * PHP versions 5.3+
 *
 * flexMVC: Rapid Development Framework
 * Created by Michael Munsie
 *
 * @package       flexMVC
 * @version       flexMVC V.1
 */

// Get Application Configuration
require_once(APPLICATION."config.php");

// Autoloader
function __autoload($class) {
	$class = strtolower($class);
	$params = explode("\\", $class);
	$class_name = "_".$params[count($params)-1];
	$location = str_replace("\\", "/", VIEWS."_".$class.PHP_EXT);
	if(isset($params[1])){
		switch($params[0]){
			case "models": 
				$location = str_replace("\\", "/", APPLICATION.$class.PHP_EXT); 
				break;
			case "api": 
				$location = str_replace("\\", "/", APPLICATION.$class.PHP_EXT);
				break;
		}
	}
	$location2 = str_replace("\\", "/", APPLICATION."libs/".$class.PHP_EXT);
	$found_file = false;
	if(file_exists($location)){ 
		include_once($location);
		$found_file = true;
	}elseif(file_exists($location2)){ 
		include_once($location2);
		$found_file = true;
	}
	if($found_file){
		if(method_exists($class, "init")) call_user_func(array($class,"init"));
	}

} 

class flexMVC{

	// Get the parameters from URL
	static $params = false;
	static $get = false;
	static $post = false;
	static $request = false;
	static $files = false;
	static $scripts = array();
	static $css = array();

	// Controller Variables
	static $view = false;		
	static $title = "";
	static $bodyclass = "";
	static $layout = "default";	
	
	// Misc
	static $error_page = "errors";

/**
 * Initialize Web Application
 *
 * @return void
 * @access public
 */	
	public static function init_web_app(){
		@session_start();
		flexMVC::check_httpprotocol();
		flexMVC::define_config();	
		flexMVC::sanatize_form_input();			
		flexMVC::check_api();			
		flexMVC::get_view();	
		flexMVC::render();		
	}

/**
 * Add script
 *
 * @return void
 * @access public
 */	
	public function add_scripts(){
		$args = func_get_args();
		if(!empty($args)){
			foreach($args as $script){
				flexMVC::$scripts[] = $script;
			}
		}
	}

/**
 * Output Scripts
 *
 * @return void
 * @access public
 */	
	public function output_scripts(){
		if(!empty(flexMVC::$scripts)){
			foreach(flexMVC::$scripts as $script){
				echo("<script type=\"text/javascript\" src=\"" . $script . "\"></script>");
			}
		}
	}		

/**
 * Add script
 *
 * @return void
 * @access public
 */	
	public function add_css(){
		$args = func_get_args();
		if(!empty($args)){
			foreach($args as $script){
				flexMVC::$css[] = $script;
			}
		}
	}		

/**
 * Output CSS
 *
 * @return void
 * @access public
 */	
	public function output_css(){
		if(!empty(flexMVC::$css)){
			foreach(flexMVC::$css as $script){
				echo("<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $script . "\"/>");
			}
		}
	}			
	
/**
 * Check HTTP Protocol
 *
 * @return void
 * @access public
 */		
	public static function check_httpprotocol(){
		if(HTTPS_ONLY){
			if ($_SERVER['SERVER_PORT'] != 443){
				$url = "https://". $_SERVER['SERVER_NAME'] . ":443".$_SERVER['REQUEST_URI'];
				header("Location: $url");
			}
		}
		if($_SERVER['SERVER_PORT'] == 443) define('HTTP_PROTOCOL', 'https://');
		else define('HTTP_PROTOCOL', 'http://');
	}	
	
/**
 * Define Core Configuration
 *
 * @return void
 * @access public
 */		
	public static function define_config(){
	
		// Define Ajax Request
		$ajax_request = false;
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
			if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') $ajax_request = true;
		}
		define('IS_AJAXREQUEST', $ajax_request);			
	
		// Define Engine Paths
		define('CSS', "public/css/");
		define('JS', "public/js/views/");
		define('VIEWS', DOCUMENT_ROOT."views/");

		// Get Layout, title, paths, etc
		flexMVC::get_paths($_SERVER['REQUEST_URI']);
		flexMVC::$layout = DEFAULT_LAYOUT;	
		
		// Define PHP INI
		ini_set('display_errors', DISPLAY_ERRORS);
		ini_set('error_reporting', E_ALL ^ E_DEPRECATED);
		ini_set('log_errors', LOG_ERRORS);
		ini_set('error_log', APPLICATION."/errors.log");
	}	
	
/**
 * Get Form Input
 *
 * @return void
 * @access public
 */		
	public static function sanatize_form_input(){
		
		// Get form data passed from redirect
		if(isset($_SESSION['flexMVC_formdata'])){

			// Make sure we are only keeping post data to the page directed to
			if(HTTP_PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'] != $_SESSION['flexMVC_formdata']['url']){
				unset($_SESSION['flexMVC_formdata']);

			// Make sure we have data before we set POST and GET variables
			}else{
				if(!empty($_SESSION['flexMVC_formdata']['post'])) $_POST = $_SESSION['flexMVC_formdata']['post'];
				if(!empty($_SESSION['flexMVC_formdata']['get'])) $_GET = $_SESSION['flexMVC_formdata']['get'];
			}
		}			
	
		// Get all "post" variables from post data or query
		foreach($_POST as $key => $value){
			if(is_array($value)){
				$vals = null;
				foreach($value as $val){ 
					if(get_magic_quotes_gpc()) $val = stripslashes($val);
					$vals[] = $val;
				}
				flexMVC::$post[$key] = $vals;
			}else{
				$value = trim($value);
				if(get_magic_quotes_gpc()) $value = stripslashes($value);
				if(is_numeric($value)) flexMVC::$post[$key] = $value;
				else{
					flexMVC::$post[$key] = $value;
				}
			}
		}

		// Get all "get" variables from post data or query
		foreach($_GET as $key => $value){
			if(is_array($value)){
				$vals = null;
				foreach($value as $val){ 
					if(get_magic_quotes_gpc()) $val = stripslashes($val);
					$vals[] = $val;
				}
				flexMVC::$get[$key] = $vals;
			}else{
				if(get_magic_quotes_gpc()) $value = stripslashes($value);
				$value = trim($value);
				if(is_numeric($value)) flexMVC::$get[$key] = $value;
				else{
					flexMVC::$get[$key] = $value;
				}
			}
		}

		// Get all "file" variables from post data or query
		foreach($_FILES as $key => $value){ 
			if(!empty($value['name'])){
				flexMVC::$files[$key] = $value;
				flexMVC::$files[$key]['form_name'] = $key;
			}
		}	
		
		// Lastly, for lazy post/get data
		$_post = is_array(\flexMVC::$post) ? \flexMVC::$post : array();
		$_get = is_array(\flexMVC::$get) ? \flexMVC::$get : array();
		\flexMVC::$request = array_merge($_post, $_get);
	}	
	
/**
 * Check for API
 *
 * @return void
 * @access public
 */ 	
	public static function check_api(){
		if(isset(flexMVC::$get['api'])){
			$class = flexMVC::$get['api'];
			$class = \StringTools::replace_last_occurrence($class, "/", "::");
			$class = str_replace("/","\\",$class);
			$class = explode("::", $class);
			$class[0] = "API\\" . $class[0];
			call_user_func(array($class[0],$class[1])); 
		}
	}	
	
/**
 * Get Controller and View
 *
 * @return void
 * @access public
 */ 		
	public static function get_view($params=false){
		if($params == false) $params = flexMVC::$params;
		if(isset($params[0])){
			if(substr($params[0],0,1) == "_"){
				define("CURRENT_VIEW_FOLDER_LOCATION", "errors");
				define("CURRENT_VIEW", "errors");
				return flexMVC::render();
			}
		}
		$counter = 0;
		$folder = "";
		$current_view_folder = "";
		if(isset($params[$counter])){
			$current_view_folder = "";
			$location = VIEWS.$folder.$params[$counter];
			$running = true;
			while($running){	
				if(is_dir($location)){ 
					$current_view_folder.= $params[$counter]."/";
					$counter++;
					if(!isset($params[$counter])) break;
					$folder.= $params[$counter-1]."/";
					$location = VIEWS.$folder.$params[$counter];
				}else{
					$running = false;
				}
			}
		}
		if(!isset($params[$counter])){ 
			$current_view = "index";
			$current_view_action = "index";
		}
		else{ 
			$current_view = $params[$counter];
			if(isset($params[$counter+1])) $current_view_action = $params[$counter+1];
			else $current_view_action = "index";
		}	
		define('CURRENT_VIEW', $current_view);
		define('CURRENT_VIEW_FOLDER', $current_view_folder);
		define('CURRENT_VIEW_FOLDER_LOCATION', VIEWS.$current_view_folder);
		define('CURRENT_VIEW_ACTION', $current_view_action);

	}
	
	
/**
 * Render the content based on controller and action
 *
 * @return void
 * @access public
 */
	private static function render(){
		if(flexMVC::$view) $viewpath = VIEWS.flexMVC::$view.VIEWPAGE_EXT;
		else $viewpath = CURRENT_VIEW_FOLDER_LOCATION.CURRENT_VIEW.VIEWPAGE_EXT;
		if(!is_file($viewpath)){
			$viewpath = VIEWS.flexMVC::$error_page.VIEWPAGE_EXT;
			if(!file_exists($viewpath)){
				echo("Please define your errors page => " . flexMVC::$error_page);
				exit;
			}
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
			ob_start();
			include($viewpath);
			$content = ob_get_clean();
			flexMVC::get_layout(VIEWS."_layouts/".flexMVC::$layout.VIEWPAGE_EXT, $content);
		}else{
			ob_start();
			include($viewpath);
			$content = ob_get_clean();
			flexMVC::get_layout(VIEWS."_layouts/".flexMVC::$layout.VIEWPAGE_EXT, $content);
			$_SESSION['flexMVC_lastActive'] = strtotime("now");
		}
	}
	
/**
 * Get Controller and Action from URL
 *
 * @param string $view_path
 * @return array
 * @access public
 */
	private static function get_paths($view_path){
		if(substr($view_path, -1) != "/") $view_path.="/";
		$check = strpos($view_path, "?");
		if(strpos($view_path, substr(DEFAULT_DIR, 1))){
			if($check != false) $path = explode("/", substr($view_path, strlen(DEFAULT_DIR)+1, $check-1));
			else $path = explode("/", substr($view_path, strlen(DEFAULT_DIR)+1));
		}else{
			if($check != false) $path = explode("/", substr($view_path, 1, $check-1));
			else $path = explode("/", substr($view_path, 1));
		}
		$path = flexMVC::filter_params($path);
		flexMVC::$params = $path;
		return $path;
	}	
	
/**
 * Filter Paths (Remove Empty Paths)
 *
 * @return array
 * @access public
 */	
	private static function filter_params($path){
		foreach($path as $key => $value){
			if(strpos($path[$key],"?") !== false) $path[$key] = substr($path[$key],0,strpos($path[$key],"?"));
			if(empty($path[$key])){ 
				unset($path[$key]);
			}
		}
		return $path;
	}
		
	
/**
 * Change the layout
 *
 * @param string $layout
 * @return class
 * @access public
 */ 
    static function change_layout($layout){
		flexMVC::$layout = $layout;
	}		
	
/**
 * Redirect to a URL
 *
 * You can also pass form data and goto an
 * absolute path instead of the relative folder.
 *
 * @param string $url
 * @param boolean $form_data
 * @param boolean $abs_path
 * @param string $message
 * @return class
 * @access public
 */
	static function redirect($url, $form_data=false, $abs_path=false){
		$url = ltrim($url, '/');
		if(strpos($url, HTTP_PROTOCOL) === false){
			if($abs_path == false){ 
				$url = HTTP_PROTOCOL . $_SERVER['HTTP_HOST'] . DEFAULT_DIR . "/" . $url;
			}
		}
		if($form_data){
			unset($_SESSION['flexMVC_formdata']);
			$_SESSION['flexMVC_formdata']['url'] = $url;
			$_SESSION['flexMVC_formdata']['post'] = flexMVC::$post;
			$_SESSION['flexMVC_formdata']['get'] = flexMVC::$get;
		}
		header('LOCATION: ' . $url);
		exit;
	}	
	
/**
 * Include BaseHREF
 *
 * @param string $script
 * @return void
 * @access public
 */		
	static function include_basehref($custom=false){
		if($custom){ 
			echo("<base href=\"" . HTTP_PROTOCOL.$_SERVER['HTTP_HOST'].DEFAULT_DIR."/".$custom . "/\"/>");
		}else{
			echo("<base href=\"" . HTTP_PROTOCOL.$_SERVER['HTTP_HOST'].DEFAULT_DIR . "/\"/>");
		}
	}
	
/**
 * Include Element
 *
 * @param string $element_name
 * @return void
 * @access public
 */
	static function include_element($element_name, $extra = null){
		if(isset($extra)) extract($extra);
		require(VIEWS."_shared/".$element_name.VIEWPAGE_EXT);
	}	


/**
 * Get the Layout for a view
 *
 * @param string $path
 * @param array $content
 * @return void
 * @access public
 */
	private static function get_layout($path, $content){
		if(is_file($path)) include($path);
		else echo("Please Define this layout path: $path");
	}

/**
 * Force refresh of session timer
 *
 * @return void
 * @access public
 */
	private static function UpdateSessionTimer(){
		$_SESSION['flexMVC_lastActive'] = strtotime("now");
	}
}