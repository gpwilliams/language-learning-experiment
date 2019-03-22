<?php
	// Database class for connecting and interacting with MySQL
	// based on:  https://www.binpress.com/tutorial/using-php-with-mysql-the-right-way/17

	class Db {
		// The database connection
		public static $connection;

		// get insert ID
		public function getInsertId() {
			$connection = $this -> connect();
			return $connection->insert_id;
		}
		
		/* Connect to the database */
		public function connect() {    
			// Try and connect to the database if haven't already
			if(!isset(self::$connection)) {
				// Load configuration as an array. Use the actual location of your configuration file
				$config = parse_ini_file('../../../../private/config_exp4.ini'); 
				self::$connection = new mysqli('localhost',$config['username'],$config['password'],$config['dbname']);
			}

			// If connection was not successful, handle the error
			if(self::$connection === false) {
				// Handle error - notify administrator, log to a file, show an error screen, etc.
				return false;
			}
			return self::$connection;
		}

		/* Query the database */
		public function query($query) {
			// Connect to the database
			$connection = $this -> connect();
			if (!$connection) {
				return false;
			}
			
			// Query the database
			$result = $connection -> query($query);

			if($result === false) {
				// Handle failure - log the error, notify administrator, etc.
				//$error = $this -> error();
				// Send the error to an administrator, log to a file, etc.
				return false;
			}
			
			return $result;
		}

		/* Fetch rows from the database (SELECT query) */
		public function select($query) {
			$result = $this -> query($query);
			if($result === false) {
				// Handle failure - log the error, notify administrator, etc.
				//$error = $this -> error();
				// Send the error to an administrator, log to a file, etc.
				return false;
			}
			return $result;
		}

		/* Fetch the last error from the database */
		public function error() {
			$connection = $this -> connect();
			return $connection -> error;
		}

		/* Quote and escape value for use in a database query */
		public function quote($value) {
			$connection = $this -> connect();
			$value = $this->sanitiseMySQL($value);	// NIK
			return "'" . $connection -> real_escape_string($value) . "'";
		}
			
		/* Sanitisation Functions */
		public function sanitiseString($var) {
			$var = stripslashes($var);
			$var = strip_tags($var);
			$var = htmlentities($var);
			return $var;
		}
		
		public function sanitiseMySQL($var) {
			$connection = $this->connect();
			$var = $connection->real_escape_string($var);
			$var = $this->sanitiseString($var);
			return $var;
		}
	}

?>