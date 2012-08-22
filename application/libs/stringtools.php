<?php

/**
 * String Tools
 *
 * Functions for customizing your strings
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
 
class StringTools extends \flexMVC{
	
/**
 * Word Count
 *
 * @param str $str
 * @return int $count
 * @access public
 */		
	public static function word_count($str){
		return count(explode(" ", $str));
	}
	
/**
 * Trim string to word count
 *
 * @param str $str
 * @return int $desired_word_count
 * @access public
 */			
	public static function trim_to_word_count($str, $desired_word_count){
		$dissected_str = explode(" ", $str);
		$dissected_str_length = count($dissected_str);
		$new_str = $space = "";
		for($x=0;$x<$desired_word_count;$x++){ 
			if($x >= $dissected_str_length) break;
			$new_str.= $space.$dissected_str[$x];
			$space = " ";
		}
		return $new_str;
	}
	
/**
 * Strip HTML tags and trim string (shortcut)
 *
 * @param str $str
 * @return int $desired_word_count
 * @access public
 */	
	public static function striphtml_and_trim($str, $desired_word_count){
		$str = strip_tags($str);
		return StringTools::trim_to_word_count($str, $desired_word_count);
	}

/**
 * Crop Text
 *
 * @param str $str
 * @param int $word_count
 * @return str $setence
 * @access public
 */	
	public static function crop_text($str, $word_count, $allowed_tags=false){
		$str = strip_tags($str, $allowed_tags);
		$str = preg_replace('/\s+/',' ', $str);
		$words = explode(" ", $str);
		$sentence = $space = "";
		for($x=0;$x<$word_count;$x++){ 
			$sentence.= $space.$words[$x];
			$space = " ";
		}
		if($word_count < count($words)) $sentence.="...";
		return $sentence;
	}	

/**
 * String to Underscore
 *
 * @param str $str
 * @return str $str
 * @access public
 */		
	public static function underscore($str){
		$str = preg_replace('/[\'"]/', '', $str);
		$str = preg_replace('/[^a-zA-Z0-9]+/', '_', $str);
		$str = trim($str, '_');
		$str = strtolower($str);
		return $str;
	}

/**
 * Replace nth occurence
 *
 * @param str $haystack
 * @param str $needle
 * @param str $n 
 * @param str $replace
 * @return str $str
 * @access public
 */			
	function replace_nth_occurrence($haystack, $needle, $n, $replace) {
		$pos = $last = 0;
		$counter = 0;
		while(strpos($haystack, $needle, $pos+1) !== false){
			$pos = strpos($haystack, $needle, $pos+1);
			$counter++;
			if($counter == $n) break;	
		}
		return substr_replace($haystack, $replace, $pos, strlen($needle));
	}	
	
/**
 * Replace last occurrence
 *
 * @param str $haystack
 * @param str $needle
 * @param str $replace
 * @return str $str
 * @access public
 */			
	public static function replace_last_occurrence($haystack, $needle, $replace) {
		return self::replace_nth_occurrence($haystack, $needle, -1, $replace);
	}		
} 
