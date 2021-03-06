<?php
/**
* PleaseSQL is a dead-simple php 5+ class that will painlessly execute any SQL query as long as you ask it kindly. 
*  
* FEATURES :
* - Connect to the database (on construction)
* - Execute queries 
* - Restore database from file
* - Automatically restore database from file if given date expired
* - Disconnect from database (on destruction)
* 
* @author Hubert Fauconnier
* @copyright Copyright © 2016 Hubert Fauconnier
* @license http://www.wtfpl.net/ WTFPL – Do What the Fuck You Want to Public License
* @link https://github.com/greird/pleasesql
*/
class PleaseSQL
{
	/**
	* ///////////////////////////////////////////
	* SETTINGS - Please insert your database information below
	*/
	private $host 			= 'localhost';
	private $database 		= 'test';
	private $username 		= 'root';
	private $password 		= 'root';
	private $timestampFile 	= "timestamp.ini"; // file will be created if it doesn't exist
	/**
	* ///////////////////////////////////////////
	*/

	private $credentials 	= array();	// Will contain dsn, username and password for database connection
	private $db 			= null;		// Will contain PDO object
	private $query 			= null;		// will contain the query to execute
	public  $getStatus 		= '';		// will contain the status of the query
	
	/**
	* Connect to database on construction
	*/
	function __construct()
	{
		if(empty($this->host) || empty($this->database) || empty($this->username)) {
			die('Woops, your database information seems incorrect. Host name, database name and username cannot be empty.');
			// Note: password can be empty (e.g. connection to a local database)
		}
		else {
			$this->credentials = array(
				"dsn" => "mysql:host=".$this->host.";dbname=".$this->database,
				"username" => $this->username,
				"password" => $this->password,
			);

			try {
				$this->db = new PDO($this->credentials['dsn'], $this->credentials['username'], $this->credentials['password']);
			} catch (Exception $e) {
				die ('Woops, could not connect to database: ' . $e->getMessage());
			}
		}
	}

	/**
	* Execute a query and return the results either as an array or a string
	* @param $sql string Sql query
	* @param $fetchType int 2 will fetch all values and return an array, 1 will fetch the first value and return a string, 0 will only return 'true' on success		
	* @return array/string/boolean Return either an array of all values or a string if $fetchType = 'single'
	*/
	function execute($sql, $fetchData = 0) 
	{
		$this->query = $this->db->prepare($sql);
		$this->query->execute();
		
		switch ($fetchData) {
			case 2:
				// store all results in an array
				return $this->query->fetchAll(PDO::FETCH_ASSOC); 
				break;
			case 1:
				// store the first value in a string variable
				return $this->query->fetch(PDO::FETCH_COLUMN, 0);
				break;
			case 0:
				// return true on success
				return true; 
				break;
			default:
				die('Oh dear ! It looks like you\'ve entered a wrong parameter: '.$fetchData.'. Expected 0, 1 or 2.');
				break;
		}

		unset($this->query);
	}

	/**
	* Restore a table from a given sql file
	* @param $tableName string Name of the table to truncate
	* @param $filePath string Path to an sql file containing a simple INSERT query
	* @return boolean Return true on success
	*/
	function restore($tableName, $filePath)
	{
		// Store the content of the sql file in a variable
		$handle = fopen($filePath, "r") or die('Cannot open file '.$filePath);
		$sql = fread($handle, filesize($filePath));
		fclose($handle);

		// Make sure the table is empty
		$this->execute('TRUNCATE '.$tableName, 0);

		// Execute the sql file
		$this->execute($sql);

		return true;
	}

	/**
	* Automatically restore a table from a given sql file when it's out of date
	* @param $tableName string Name of the table to truncate
	* @param $filePath string Path to an sql file containing a simple INSERT query
	* @param $delay int If current timestamp - stored timestamp >= $delay, table will be restored
	* @return boolean Return true on success, false if timestamp didn't expired
	*/
	function autoRestore($tableName, $filePath, $delay)
	{
		// If file doesn't exist, create it
		if(file_exists($this->timestampFile)) {
			// open and read file $file
			$handle = fopen($this->timestampFile, "r+") or die('Cannot open file:  '.$this->timestampFile);
			
		} else {
			$handle = fopen($this->timestampFile, "w+") or die('Cannot open file:  '.$this->timestampFile);
			fwrite($handle, time());
		}
		// Store file content in a variable
		$lastResetDate = fread($handle, filesize($this->timestampFile));

		// If timestamp has expired, restore table
		if(time() - $lastResetDate > $delay) {
			// Replace the content of the file by the current timestamp
			rewind($handle);
			fwrite($handle, time());
			fclose($handle);

			$this->restore($tableName, $filePath);

			$this->getStatus .= 'TABLE RESTORED<br /><br />';
			$this->getStatus .= 'Last db reset: '.date('d/m H\hi' ,time()).'<br />';
			$this->getStatus .= 'Next data reset: '.date('d/m H\hi' ,$delay + time()).'<br />';

			return true;
		}
		// There's time left
		else {
			fclose($handle);

			$this->getStatus .= 'WAITING..<br /><br />';
			$this->getStatus .= 'Last db reset: '.date('d/m H\hi' ,$lastResetDate).'<br />';
			$this->getStatus .= 'Next data reset: '.date('d/m H\hi' ,($delay - (time() - $lastResetDate)) + time()).'<br />';

			return false;
		}

	}

	/**
	* Disconnect from database on destruction
	*/
	function __destruct() {
		unset($this->db);
	}
}
?>