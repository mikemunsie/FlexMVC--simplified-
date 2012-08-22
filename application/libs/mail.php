<?php

/**
 * Mail Class
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

class Mail extends \flexMVC{

	public static $from_name = false;
	public static $from_email = false;
	public static $subject = false;
	public static $mail_layout = false;
	public static $data = false;
	public static $body = false;
	public static $to = false;
	public static $preview = false;

/**
 * Send an Email
 *
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */
	public static function send(){
	
		$error = false;
	
		if(Mail::$mail_layout){
			$file = APPLICATION."layouts/mail/".Mail::$mail_layout.PHP_EXT;
			if(is_file($file)){
				if(Mail::$data) extract(Mail::$data); 
				ob_start();
				include($file);
				Mail::$body = ob_get_clean();
			}else{
				$error = true;
				trigger_error("Mail Layout: '" . Mail::$mail_layout . "' does not exist.", E_USER_NOTICE);
			}
		}
	
		if(Mail::$to == false){ 
			$error = true;
			trigger_error("Please provide a 'to' address to send the email.", E_USER_NOTICE);
		}
		
		if(Mail::$body == false){
			$error = true;
			trigger_error("Please provide 'body' information or use an existing layout. ", E_USER_NOTICE);
		}
		
		if(Mail::$from_name == false){
			$error = true;
			trigger_error("Please provide a 'from_name'  to send the email.", E_USER_NOTICE);
		}
		
		if(Mail::$from_email == false){
			$error = true;
			trigger_error("Please provide a 'from_email' to send the email.", E_USER_NOTICE);
		}
		
		if(Mail::$subject == false){
			$error = true;
			trigger_error("Please provide a 'subject' line to send the email.", E_USER_NOTICE);
		}
		
		
		if(!$error){
			if(Mail::$preview){
				echo(Mail::$body);
				exit;
			}
			ini_set('sendmail_from', 'me@domain.com');
			if(mail(Mail::$to, Mail::$subject, Mail::$body, "From: " . Mail::$from_name . " <" . Mail::$from_email . ">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\nContent-type: text/html; charset=iso-8859-1")) return true;
		}
		
		return false;
	}
}