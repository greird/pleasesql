<?php
/**
* Connect to the database
* Execute queries 
* Disconnect from database
*/
class DB
{
	/**
	* ///////////////////////////////////////////
	* 
	* SETTINGS - database information
	*/
	private $host 			= 'localhost';
	private $database 		= 'test';
	private $username 		= 'root';
	private $password 		= 'root';

	/**
	* DO NOT EDIT BELOW THIS LINE
	* ///////////////////////////////////////////
	*/
	private $credentials 	= array(); 	// Will contain dsn, username and password for database connection
	private $db 			= null;		// Will contain PDO object
	private $query 			= null;		// will contain the query to execute
	
	/**
	* Connect to database on construction
	*/
	function __construct()
	{
		if(empty($this->host) || empty($this->database) || empty($this->username) {
			die('Incorrect database information. Host name, database name and username cannot be empty.');
			// Note: password can be empty (e.g. connection to a local database)
		}
		else {
			$this->$credentials = array(
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
	* @param $sql string 		| Sql query
	* @param $fetchType int 	| 2 will fetch all values, 1 will fetch only the next value, 0 will only return 'true' on success		
	* @return array / string 	| Return either an array of all values or a string if $fetchType = 'single'
	*/
	function execute($sql, $fetchData = 0) 
	{
		$this->query = $this->db->prepare($sql);

		# conflit de nom entre les fonction execute ??
		$this->query->execute() or die('Could not execute query.');
		
		// return an array of all values
		if($fetchData === 2) {
			return $this->query->fetchAll(PDO::FETCH_ASSOC); 
		}
		// return a single value
		else if($fetchData === 1) {
			return $this->query->fetch(PDO::FETCH_COLUMN, 0); 
		}
		// return true on success
		else if ($fetchData === 0) {
			return true; 
		}
		// error
		else {
			die('Wrong parameter '.$fetchData.'. Expected 0, 1 or 2.');
		}

		unset($this->query);
	}

	/**
	* TOOLS
	*/
	function truncate($tableName) 
	{
		$this->execute('TRUNCATE '.$tableName, 0);
	}
	function drop($tableName) 
	{
		$this->execute('DROP '.$tableName, 0);
	}

	/**
	* Disconnect from database on destruction
	*/
	function __destruct() {
		unset($this->db);
	}
}
?>