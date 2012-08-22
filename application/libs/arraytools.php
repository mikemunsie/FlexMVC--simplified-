<?php 

/**
 * Array Tools
 *
 * Functions for customizing your arrays
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
 
class ArrayTools extends \flexMVC{

/**
 * Sort an array by column
 *
 * Example:
 * $results = \Libs\ArrayTools::sort($array, "orderid", "DESC"); 
 *
 * @param array $array
 * @param string $custom_key
 * @param string $orderby
 * @access public
 * @return array
 */		
	public static function sort($array, $custom_key, $orderby = null){
		foreach($array as $row=>$col) $sort_array[$row] = strtolower($col[$custom_key]);
		asort($sort_array);
		foreach($sort_array as $key=>$val) $new_array[] = $array[$key];
		if($orderby == "DESC") return array_reverse($new_array);
		return $new_array;
	}

/**
 * Order an array by columns
 *
 * Example:
 * $results = $this->ArrayTools->orderByColumns($array, array("orderid", "firstname", "lastname"));
 *
 * @param array $array
 * @param string $custom_key
 * @param string $orderby
 * @access public
 * @return array
 */			
	public static function orderByColumns($array, $columns){	
		foreach($columns as $column){
			$counter = -1;
			if(isset($array[0])){
				foreach($array as $row){ 				
					$rows[++$counter][$column] = $row[$column];
				}
			}else{ 
				$rows[++$counter][$column] = $array[$column];
			}
		}	
		if(isset($array[0])) return $rows;
		else return $rows[0];
	}	
	
/**
 * Multi-row in_array search
 *
 * @param str $str
 * @param array $array
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	public static function multi_in_array($str, $array, $val = null){
		$counter=-1;
		foreach($array as $row){
			$counter++;
			if(in_array($str, $row)){
				if($val) return $row[$val];
				return $row;
			}
		}
		return false;
	}	
	
/**
 * Search through an array used for Validator
 *
 * @param string $needle
 * @param array $haystack
 * @access public
 */ 		
	public static function array_nsearch($needle, array $haystack){
		$it = new IteratorIterator(new ArrayIterator($haystack));
		foreach($it as $key => $val) {
		   if(strcasecmp($val,$needle) === 0) return TRUE;
		}
	}		
	
/**
 * Multidimensional form array sort
 *
 * @param array $data
 * @return mixed FALSE if cannot sort array, $array on success
 * @access public
 */ 	
	public static function multi_formelements_sort($data){
		
		// Filter out single dimensional arrays (could be used for other data)
		foreach($data as $key => $value){ 
			if(!isset($data[$key][0])) unset($data[$key]);
		}
		
		if(empty($data)) return false;
		
		// Setup a key counter
		$total_keys = count(array_keys($data));
				
		// Go through each array and make new array
		foreach($data as $key => $array){
			$counter = -1;
			foreach($array as $value){
				$counter++;
				$new_array[$counter][$key] = $value;
			}
		}
		
		// Verify each array matches the count of keys passed on first array
		foreach($new_array as $array){
			if(count($array) != $total_keys){ 
				trigger_error("Form element count is not consistent. Database will not update/insert as expected.", E_USER_NOTICE);
				return false;
			}
		}
		return $new_array;
	}  
	
/**
 * Make array unique by specified key
 *
 * @param array $data
 * @return mixed FALSE if cannot sort array, $array on success
 * @access public
 */ 		
	public static function unique_by_key($array=false, $sort_key=false){
		if(!$array || !$sort_key) return false;
		$stored_values = array();
		foreach($array as $key => $value){
			if(!isset($value[$sort_key])) unset($array[$key]);
			if(in_array($value[$sort_key], $stored_values)) unset($array[$key]);
			else $stored_values[] = $value[$sort_key];
		}
		return $array;
	}
	
} 
