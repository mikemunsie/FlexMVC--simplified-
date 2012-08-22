<?php

/**
 * Validation Class
 *
 * Functions for validating forms and content >> Imported Class <<
 * Validation Class imported from CakePHP (See: http://cakephp.org)
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

class Validate extends \flexMVC{

/**
 * Regex array contains patterns for string validation
 *
 * @public static regex
 * @access public
 */	
	public static $regex = array('zipcode' => '/^([0-9]{5})(-[0-9]{4})?$/i',
					   'notEmpty' => '/.+/',
					   'number' => '/^[-+]?\\b[0-9]*\\.?[0-9]+\\b$/',
					   'email' => '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i',
					   'year' => '/^[12][0-9]{3}$/',
					   'alphaNumeric' => '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/mu',
					   'empty' => '/[^\\s]/',
					   'phoneNumber' => '/^(?:\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$/',
					   'date_dmy' => '%^(?:(?:31(\\/|-|\\.|\\x20)(?:0?[13578]|1[02]))\\1|(?:(?:29|30)(\\/|-|\\.|\\x20)(?:0?[1,3-9]|1[0-2])\\2))(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$|^(?:29(\\/|-|\\.|\\x20)0?2\\3(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\\d|2[0-8])(\\/|-|\\.|\\x20)(?:(?:0?[1-9])|(?:1[0-2]))\\4(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$%',
					   'date_mdy' => '%^(?:(?:(?:0?[13578]|1[02])(\\/|-|\\.|\\x20)31)\\1|(?:(?:0?[13-9]|1[0-2])(\\/|-|\\.|\\x20)(?:29|30)\\2))(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$|^(?:0?2(\\/|-|\\.|\\x20)29\\3(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:(?:0?[1-9])|(?:1[0-2]))(\\/|-|\\.|\\x20)(?:0?[1-9]|1\\d|2[0-8])\\4(?:(?:1[6-9]|[2-9]\\d)?\\d{2})$%',
					   'date_ymd' => '%^(?:(?:(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\\/|-|\\.|\\x20)(?:0?2\\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\\d)?\\d{2})(\\/|-|\\.|\\x20)(?:(?:(?:0?[13578]|1[02])\\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\\2(?:0?[1-9]|1\\d|2[0-8]))))$%',
					   'date_dMy' => '/^((31(?!\\ (Feb(ruary)?|Apr(il)?|June?|(Sep(?=\\b|t)t?|Nov)(ember)?)))|((30|29)(?!\\ Feb(ruary)?))|(29(?=\\ Feb(ruary)?\\ (((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\\d|2[0-8])\\ (Jan(uary)?|Feb(ruary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(?=\\b|t)t?|Nov|Dec)(ember)?)\\ ((1[6-9]|[2-9]\\d)\\d{2})$/',
					   'date_Mdy' => '/^(?:(((Jan(uary)?|Ma(r(ch)?|y)|Jul(y)?|Aug(ust)?|Oct(ober)?|Dec(ember)?)\\ 31)|((Jan(uary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sept|Nov|Dec)(ember)?)\\ (0?[1-9]|([12]\\d)|30))|(Feb(ruary)?\\ (0?[1-9]|1\\d|2[0-8]|(29(?=,?\\ ((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))))\\,?\\ ((1[6-9]|[2-9]\\d)\\d{2}))$/',
					   'date_My' => '%^(Jan(uary)?|Feb(ruary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(?=\\b|t)t?|Nov|Dec)(ember)?)[ /]((1[6-9]|[2-9]\\d)\\d{2})$%',
					   'date_my' => '%^(((0[123456789]|10|11|12)([- /.])(([1][9][0-9][0-9])|([2][0-9][0-9][0-9]))))$%',
					   'time' => '%^((0?[1-9]|1[012])(:[0-5]\d){0,2}([AP]M|[ap]m))$|^([01]\d|2[0-3])(:[0-5]\d){0,2}$%',
					   'decimal' => '/^[-+]?[0-9]*\\.{1}[0-9]+(?:[eE][-+]?[0-9]+)?$/',
					   'money' => '/^(?!\x{00a2})\p{Sc}?(?!0,?\d)(?:\d{1,3}(?:([, .])\d{3})?(?:\1\d{3})*|(?:\d+))((?!\1)[,.]\d{2})?$/u');
/**
 * Check if an array contains required fields
 *
 * @param array $array
 * @param array $required_fields
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function required_fields($array, $required_fields, $check_empty=true){
		foreach($required_fields as $key => $value){
			if(empty($required_fields)) break;
			if(array_key_exists($value, $array)){ 
				if($check_empty){
					if(!empty($array[$value])) unset($required_fields[$key]);
				}else unset($required_fields[$key]);	
			}
		}
		if(empty($required_fields)) return true;
		return false;
	}
	
/**
 * Validate a zip code
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function zipcode($check){
		if(Validate::check('zipcode', $check)) return TRUE;
		return FALSE;
	}		
	
/**
 * Check if a string contains only letters
 *
 * @param string $check
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */	
	public static function letters($check){
		$check = str_replace(" ","", $check);
		return ctype_alpha($check);
	}	
	
	
/**
 * Check if a string contains only numbers
 *
 * @param string $check
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
	public static function numbers($check){
		$check = str_replace(" ","", $check);
		return ctype_digit($check);
	}
	
/**
 * Verify String Length
 *  
 * @param $string string
 * @return boolean
 * @access public
 */ 	
	public static function verify_stringlength($string, $min, $max){	
		if(strlen($string) >= $min && strlen($string) <= $max) return TRUE;;
	}	

/**
 * Validate an email
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
 	public static function email($check){
		if(Validate::check('email', $check)) return TRUE;
		return FALSE;
	}
/**
 * Validate an phone number
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function phone($check){
		if(Validate::check('phoneNumber', $check)) return TRUE;
		return FALSE;
	}
	
/**
 * Validate an alphabetic string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
	public static function alphabetic($check){
		return ctype_alpha($check);
	}
	
/**
 * Validate an alphanumerical string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
	public static function alphaNumeric($check){
		return ctype_alnum($check);
	}

/**
 * Validate a string based on custom regex pattern
 *
 * @param string $check String you want validated
 * @param string $regex Custom pattern you are checking
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
	public static function check($regex, $check){
		if(preg_match(Validate::$regex[$regex], $check)) return TRUE;
		return FALSE;
	}

/**
 * Validate a non-empty string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function notEmpty($check) {
		if(Validate::check('empty', $check)) return TRUE;
		return FALSE;
	}

/**
 * Validate a date-type string
 *
 * ### Usage:
 *
 * {{{
 * Validate::Validate->date('dmy', 'date checking here');
 * }}}
 *
 * @param string $type Type of date you want checked
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function date($type, $check) {
		if(Validate::check('date_'.$type, $check)) return TRUE;
		return FALSE;
	}
	
/**
 * Validate a time-based string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function time($check) {
		if(Validate::check('time', $check)) return TRUE;
	}
	
/**
 * Validate a decimal-based string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function decimal($check) {
		if(Validate::check('decimal', $check)) return TRUE;
	}
	
/**
 * Validate a money-based string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */			
	public static function money($check) {
		if(Validate::check('money', $check)) return TRUE;
	}
	
/**
 * Validate a url-based string
 *
 * @param string $check String you want validated
 * @return boolean TRUE if string is correct, FALSE if  string is not correct
 * @access public
 */		
	public static function url($check){
		return filter_var($check, FILTER_VALIDATE_URL);
	}	
}
