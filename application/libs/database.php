<?php

/**
 * MySQL Database Class
 *
 * static functions for making transactions using MySQL only
 *
 * PHP versions 5
 *
 * munsieMVC: Rapid Development Framework
 * Created by Michael Munsie
 * 
 * @copyright     Copyright 2011-2012, Michael Munsie, http://mikemunsie.com
 * @link          http://mikemunsie.com
 * @package       munsieMVC
 * @version       munsieMVC V.5
 */
 
class Database extends flexMVC{

    public static $connections;										// Holds the different connection
    public static $default_connection = DEFAULT_DBCONNECTION;		// The defaulted connection from config
    public static $current_connection = DEFAULT_DBCONNECTION;		// Current Connection
	public static $auto_rollback = false;							// Auto Rollback
	public static $success = false;									// Holds the success of the query
	public static $results = false;									// Holds the results
	public static $error = false;									// Error message if applicable
	
/**
 * Constructor
 */	
	public static function init(){
		Database::$connections	= $_ENV['database'];		
	}		
	
/**
 * Start a transaction
 *
 * @return void
 * @access public
 */		
	public static function start_transaction(){
		Database::query("BEGIN WORK");
	}
	
/**
 * Start a transaction and auto rollback if an error occured
 *
 * @return void
 * @access public
 */			
	public static function start_auto_rollback(){
		Database::$auto_rollback = true;
		Database::start_transaction();
	}
	
/**
 * Rollback a transaction
 *
 * @return void
 * @access public
 */			
	public static function rollback(){
		Database::query("ROLLBACK");
	}
	
/**
 * Stop a transaction
 *
 * @return void
 * @access public
 */		
	public static function stop_transaction(){
		Database::$auto_rollback = false;
		Database::query("COMMIT");
	}	
	
/**
 * Send return from database query
 * 
 * Hold the success, error, and results generated from
 * from a MySQL Query
 *
 * @param string $connection
 * @return void
 * @access public
 */		
	public static function send_return($success=false, $error=false, $results=false){
		Database::$success = false;
		Database::$error = false;
		Database::$results = false;
		if($success) Database::$success = $success;
		if($error) Database::$error = $error;
		if($results) Database::$results = $results;
		if($error){ 
			if(Database::$auto_rollback) Database::rollback();
		}
		return array("success" => Database::$success,
					 "results" => Database::$results,
					 "error" => Database::$error);
	}
	
/**
 * Set default database connection
 *
 * @param string $connection
 * @return void
 * @access public
 */		
	public static function set_default_db($connection){
		$connection  = strtolower($connection);
		Database::$default_connection = $connection;
	}
	
/**
 * Set current database connection
 *
 * @param string $connection
 * @return void
 * @access public
 */			
	public static function set_current_db($connection){
		$connection = strtolower($connection);
		Database::$current_connection = $connection;
	}		
	
/**
 * Use Default Database (according to config)
 *
 * @return void
 * @access public
 */		
	public static function use_default_db(){
		Database::$current_connection = Database::$default_connection;
		return Database::$current_connection;
	}
	
/**
 * Add a new connection
 *
 * @param array $connection (type, database, host, username, password)
 * @param string $connection
 * @param boolean $grab_results=false
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */
	public static function add_connections($connection){
		foreach($connection as $name => $params) Database::$connections[$name] = $params;
	}	
	
/**
 * Change current connection
 *
 * @param string $connection
 * @return void
 * @access public
 */	
	public static function change_current_connection($connection){
		Database::$current_connection = $connection;
	}
	
/**
 * 
 * Override_hostpec (switch to a different IP)
 * 
 * Override hostpec of a connection
 * 
 * @access public
 * @param $connection
 * @param $hostspec
 * @return void
 */
	public static function override_hostspec($connection, $hostspec){
		
		// Make sure connection exists
		if(isset(Database::$connections[$connection])){
			
			Database::$connections[$connection]['hostspec'] = $hostspec;
			
			// Check if the connection is already established
			if(isset(Database::$connections[$connection]['mysql'])){
				Database::disconnect($connection);
				Database::connect($connection);
			}
		}		
	}

/**
 * Connect to a database
 *
 * @param array $connection
 * @return boolean TRUE on success, FALSE on failure
 * @access private
 */
	private static function connect(){	
		
		// Use the current connection
		$connection = Database::$current_connection;
		
		// Validate the connection
		if(empty(Database::$connections[$connection])){
			return Database::send_return(false, "Connection '$connection' does not exist.");
		}

		// Make sure connection has not been established
		if(empty(Database::$connections[$connection]['mysql'])){
			
			// Try and estabish a connection object
			Database::$connections[$connection]['mysql'] = mysql_connect(Database::$connections[$connection]['hostspec'], 
																	 Database::$connections[$connection]['username'], 
																	 Database::$connections[$connection]['password']);
			
			// Check for a connection error
			if(!Database::$connections[$connection]['mysql']){		
				return Database::send_return(false, "Unable to connect: $connection", E_USER_WARNING);
			
			// Select database in connection
			}else{
				mysql_select_db(Database::$connections[$connection]['database']);
	        	return Database::send_return(true);
			}
		}					  	
	}	
	
/**
 * Disconnect a connection
 *
 * @param array $connection
 * @return boolean TRUE on success, FALSE on failure
 * @access private
 */	
	private static function disconnect(){
		
		// Use the current connection
		$connection = Database::$current_connection;	
		
		// Validate the connection
		if(empty(Database::$connections[$connection])){
			return Database::send_return(false, "Connection '$connection' does not exist.");
		}

		// Check for a connection error
		if(Database::$connections[$connection]['mysql']){		
			mysql_close(Database::$connections[$connection]['mysql']);
			unset(Database::$connections[$connection]['mysql']);
		}
		return Database::send_return(true);
	}
	
/**
 * Establish a connection if not established
 *
 * @param string $connection 
 * @return Database::connect($connection)
 * @access private
 */	 	
	private static function is_established(){
		
		// Use the current connection
		$connection = Database::$current_connection;		
		
		// Check if the connection has been established
		if(!isset(Database::$connections[$connection]['mysql'])){
			return Database::connect($connection);
		}else{
			if(!Database::$connections[$connection]['mysql']){
				return Database::send_return(false);
			}
			return Database::send_return(true);
		}
	}
	
/**
 * Make a MySQL Query
 *
 * @param string $sql
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */	 
	static function query($sql){
		
		// Use the current connection
		$connection = Database::$current_connection;

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){	
			
			// Return the query
			$result = mysql_query($sql, Database::$connections[$connection]['mysql']);
		
			// Check for a database error
			if(mysql_error(Database::$connections[$connection]['mysql'])){
				return Database::send_return(false, mysql_error(Database::$connections[$connection]['mysql']) . " \"" . $sql . '"');
			}
			else{ 
				$results = false;
				if(mysql_affected_rows(Database::$connections[$connection]['mysql']) || mysql_insert_id(Database::$connections[$connection]['mysql'])){
					$results = true;
				}
				return Database::send_return(true, false, $results);
			}
			
		}		
	}

	
/**
 * Parse results into a nice array 
 *
 * @param string $sql
 * @return mixed $table_result on success, FALSE on failure
 * @access public
 */	 		
	static function get_results($sql){
	
		// Use the current connection
		$connection = Database::$current_connection;
		
		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){
				
			// Execute the query
			$result = mysql_query($sql, Database::$connections[$connection]['mysql']);
			if($result){				
			
				// Grab the results
				$table_result=array();
				$r=0;
				while($row = mysql_fetch_assoc($result)){
					$arr_row=array();
					$c=0;
					while ($c < mysql_num_fields($result)) {       
						$col = mysql_fetch_field($result, $c);   
						$arr_row[$col -> name] = $row[$col -> name];           
						$c++;
					}   
					$table_result[$r] = $arr_row;
					$r++;
				}   		
				
				// Make sure the table isn't empty (double check)
				if(empty($table_result)) return Database::send_return(false);
				
				// Check the first row of results (if all null, then don't return)
				$columns = count($table_result[0]);
				$counter = 0;
				
				foreach($table_result[0] as $key => $value){
					if($value == NULL) $counter++;
				}
				if($counter == $columns) return Database::send_return(true);
				return Database::send_return(true, false, $table_result);
			}else return Database::send_return(false, mysql_error() . " \"" . $sql . '"');
		}
	}
	
/**
 * Show Columns from a Database table
 *
 * @param string $table
 * @return mixed $fields on success, FALSE on failure
 * @access private
 */	 	
	private static function show_columns($table){

		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){
			
			// Try and get columns from table			
			$results = Database::get_results("SHOW COLUMNS FROM " . strtolower($table));
			if($results['success']){
				foreach($results['results'] as $row) $fields[] = $row['Field'];
				return $fields;
			}	
		}
		return false;	
	}	
	
/**
 * Insert one row of data into a table
 *
 * @param string $table
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function insert($table, $data){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){		
		
			// Prepare Insert Statement
			$sql = "INSERT INTO $table (";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[$key]);
				else{
					$sql.= $comma."`$key`";
					$comma=',';
				}
			}
			
			// Prepare Values
			$sql.= ") VALUES(";
			$comma = $values = '';
			foreach($data as $key => $value){
				$value = mysql_real_escape_string($value);
				if($value == null) $values.= $comma. "null";
				else $values.= $comma. "'$value'";
				$comma=',';
			}
			$sql.= $values.")";
			if(empty($values)) return Database::send_return(true);
			return Database::query($sql);
		}
	}
	
/**
 * Insert_Update row of data into a table
 *
 * @param string $table
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function insert_update($table, $data){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){
		
			// Prepare Insert Statement
			$sql = "INSERT INTO $table (";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[$key]);
			}
			
			// Prepare the fields
			foreach($data as $key => $value){
				$sql.= $comma."`$key`";
				$comma=',';
			}
			
			// Prepare Values
			$sql.= ") VALUES (";
			$col_comma = $row_comma = $values = '';
			foreach($data as $key => $value){
				$value = mysql_real_escape_string($value);
				$values.= $col_comma. "'$value'";
				$col_comma=',';
			}
			$sql.= $values.")";
			
			// The INSERT UPDATE command ;)
			$sql.= " ON DUPLICATE KEY UPDATE ";
			$comma = "";
			foreach($data as $key => $value){
				$sql.= $comma."{$key} = VALUES({$key})";
				$comma=",";
			}
			
			// Check if we have values returned	
			if(empty($values)) return Database::send_return(true);
			return Database::query($sql);
		}
	}				
	
/**
 * Insert multiple rows of data into a table
 *
 * @param string $table
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function multi_insert($table, $data){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){
		
			// Prepare Insert Statement
			$sql = "INSERT INTO $table (";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data[0] as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[0][$key]);
			}
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data[0] as $key => $value){
				$sql.= $comma."`$key`";
				$comma=',';
			}		
			
			// Prepare Values
			$sql.= ") VALUES ";
			$col_comma = $row_comma = $values = '';
			foreach($data as $array){
				$values = $col_comma = "";
				$sql.=$row_comma."(";
				foreach($array as $key => $value){
					$value = mysql_real_escape_string($value);
					$values.= $col_comma. "'$value'";
					$col_comma=',';
				}
				$sql.= $values.")";
				$row_comma = ",";
			}
			
			// Check if we have values returned	
			if(empty($values)) return Database::send_return(true);
			return Database::query($sql);
		}
	}

/**
 * Insert_Update multiple rows of data into a table
 *
 * @param string $table
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function multi_insert_update($table, $data){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){
		
			// Prepare Insert Statement
			$sql = "INSERT INTO $table (";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data[0] as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[0][$key]);
				else{
					$sql.= $comma."`$key`";
					$comma=',';
				}
			}
			
			// Prepare Values
			$sql.= ") VALUES ";
			$col_comma = $row_comma = $values = '';
			foreach($data as $array){
				$values = $col_comma = "";
				$sql.=$row_comma."(";
				foreach($array as $key => $value){
					$value = mysql_real_escape_string($value);
					$values.= $col_comma. "'$value'";
					$col_comma=',';
				}
				$sql.= $values.")";
				$row_comma = ",";
			}
			
			// The INSERT UPDATE command ;)
			$sql.= " ON DUPLICATE KEY UPDATE ";
			$comma = "";
			foreach($data[0] as $key => $value){
				$sql.= $comma."{$key} = VALUES({$key})";
				$comma=",";
			}
			
			// Check if we have values returned	
			if(empty($values)) return Database::send_return(true);
			return Database::query($sql);
		}
	}			
	
/**
 * Update Data in a table
 *
 * @param string $table
 * @param string $where
 * @param string $key_value
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function update($table, $where, $data){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){		
		
			// Prepare update Statement
			$sql = "UPDATE $table SET ";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);	
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[$key]);
				else{
					if($value == null) $sql.= $comma."`$key`"."=null";
					else $sql.= $comma."`$key`"."='".mysql_real_escape_string($value)."'";
					$comma=',';
				}			
			}
			
			// Prepare Values
			$sql.= " WHERE $where";
			
			return Database::query($sql);
		}	
	}
	
/**
 * Insert multiple rows of data into a table
 *
 * @param string $table
 * @param array $data
 * @return boolean TRUE on success, FALSE on failure
 * @access public
 */		
	static function multi_update($table, $data, $where){
		
		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){	
		
			// Prepare Insert Statement
			$sql = "INSERT INTO $table (";
			
			// Get Table Fieldnames
			$table_fieldnames = Database::show_columns($table);
			
			// Prepare Fieldnames
			$comma = "";
			foreach($data[0] as $key => $value){
				if(!in_array($key,$table_fieldnames)) unset($data[0][$key]);
				else{
					$sql.= $comma."`$key`";
					$comma=',';
				}
			}
			
			// Prepare Values
			$sql.= ") VALUES ";
			$col_comma = $row_comma = $values = '';
			foreach($data as $array){
				$values = $col_comma = "";
				$sql.=$row_comma."(";
				foreach($array as $key => $value){
					$value = mysql_real_escape_string($value);
					$values.= $col_comma. "'$value'";
					$col_comma=',';
				}
				$sql.= $values.")";
				$row_comma = ",";
			}
			
			// Write the update command
			$sql.=" ON DUPLICATE KEY UPDATE ";
			
			$comma = "";
			foreach($table_fieldnames as $field){
				$sql.="{$comma}{$field} = VALUES({$field})";
				$comma = ",";
			}
						
			// Check if we have values returned	
			if(empty($values)) return Database::send_return(true);
			return Database::query($sql);
		}
	}		
	
/**
 * Build a safe Select statement
 *
 * @param string $sql
 * @param string @args
 * @return Database::get_results($sql)
 * @access public
 */		
	static function safe_select($sql, $args){
		$connection = Database::$current_connection;
		$sql = Database::safe_str($sql, $args);
		if($sql) return Database::get_results($sql);
		else return false;
	}	
	
/**
 * Build a safe Query statement
 *
 * @param string $sql
 * @param string @args
 * @return Database::get_results($sql)
 * @access public
 */		
	static function safe_query($sql, $args){
		$connection = Database::$current_connection;
		$sql = Database::safe_str($sql, $args);
		if($sql) return Database::query($sql);
		else return false;
	}	
	
/**
 * Return a clean SQL statement
 *
 * @param string $sql
 * @param string @args
 * @return mixed string on success, false on failure
 * @access public
 */		
	static function safe_str($sql, $args){
		$connection = Database::$current_connection;
		$results = Database::is_established($connection);
		if($results['success']){			
			foreach($args as $key => $value){
				$value = mysql_real_escape_string($value);
				$sql = str_replace(":$key","'".$value."'", $sql);
			}
			return $sql;
		}
		return false;
	}
	
/**
 * Get the last insert ID
 * 
 * @return mixed int on success, false on failure
 * @access public
 */	
	static function last_insert_id(){

		// Use the current connection
		$connection = Database::$current_connection;		

		// Make sure connection has been established
		$results = Database::is_established($connection);
		if($results['success']){	
			return mysql_insert_id(Database::$connections[$connection]['mysql']);
		}
	}
	
/**
 * Select custom
 *
 * @return int $id
 * @access public
 */			
	public static function select_custom($table=false, $where=false, $orderby=false, $limit=false){
		if(!$table) return false;
		$sql=$str=$comma="";
		$sql="SELECT * FROM $table";
		if($where){
			foreach($where as $key => $value){
				$str.= $comma.Database::safe_str("$key = :value", array("value" => $value));
				$comma=" AND ";
			}
			$sql.=" WHERE 1 AND $str";
		}
		if($orderby){
			$comma=$str="";
			foreach($orderby as $key => $value){
				$str.= $comma."$key $value";
				$comma=",";
			}
			$sql.=" ORDER BY $str";
		}
		if($limit) $sql.=" LIMIT $limit";	
		return Database::get_results($sql);
	}	
	
/**
 * Remove Custom
 *
 * @return int $id
 * @access public
 */			
	public static function remove_custom($table=false, $where=false){
		if(!$table) return false;
		if(!$where) return false;
		$sql=$str=$comma="";
		$sql="DELETE FROM $table";
		if($where){
			foreach($where as $key => $value){
				$str.= $comma.Database::safe_str("$key = :value", array("value" => $value));
				$comma=" AND ";
			}
			$sql.=" WHERE 1 AND $str";
		}
		return Database::get_results($sql);
	}				
}
