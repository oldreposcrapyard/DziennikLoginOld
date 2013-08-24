<?php
 
/**
 * PDO SINGLETON CLASS
 *  
 * @author Tony Landis
 * @link http://www.tonylandis.com
 * @license Use how you like it, just please don't remove or alter this PHPDoc
 */ 
class DB //extends PDO
{  
    /**
     * The singleton instance
     * 
     */
    static private $PDOInstance; 
     
  	/**
  	 * Creates a PDO instance representing a connection to a database and makes the instance available as a singleton
  	 * 
  	 * @param string $dsn The full DSN, eg: mysql:host=localhost;dbname=testdb
  	 * @param string $username The user name for the DSN string. This parameter is optional for some PDO drivers.
  	 * @param string $password The password for the DSN string. This parameter is optional for some PDO drivers.
  	 * @param array $driver_options A key=>value array of driver-specific connection options
  	 * 
  	 * @return PDO
  	 */
    public function __construct($dsn, $username=false, $password=false, $driver_options=false) 
    {
        if(!self::$PDOInstance) { 
	        try {
			   self::$PDOInstance = new PDO($dsn, $username, $password, $driver_options);
			} catch (PDOException $e) { 
			   die("PDO CONNECTION ERROR: " . $e->getMessage() . "<br/>");
			}
    	}
      	return self::$PDOInstance;    	    	
    }
	 
  	/**
  	 * Initiates a transaction
  	 *
  	 * @return bool
  	 */
	public function beginTransaction() {
		return self::$PDOInstance->beginTransaction();
	}
        
	/**
	 * Commits a transaction
	 *
	 * @return bool
	 */
	public function commit() {
		return self::$PDOInstance->commit();
	}
	
	/**
	 * Fetch the SQLSTATE associated with the last operation on the database handle
	 * 
	 * @return string 
	 */
    public function errorCode() {
    	return self::$PDOInstance->errorCode();
    }
    
    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array
     */
    public function errorInfo() {
    	return self::$PDOInstance->errorInfo();
    }
    
    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     */
    public function exec($statement) {
    	return self::$PDOInstance->exec($statement);
    }
    
    /**
     * Retrieve a database connection attribute
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
    	return self::$PDOInstance->getAttribute($attribute);
    }
 
    /**
     * Return an array of available PDO drivers
     *
     * @return array
     */
    public function getAvailableDrivers(){
    	return Self::$PDOInstance->getAvailableDrivers();
    }
    
    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
	public function lastInsertId($name) {
		return self::$PDOInstance->lastInsertId($name);
	}
        
   	/**
     * Prepares a statement for execution and returns a statement object 
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement obj 
returned  
     * @return PDOStatement
     */
    public function prepare ($statement, $driver_options=false) {
    	if(!$driver_options) $driver_options=array();
    	return self::$PDOInstance->prepare($statement, $driver_options);
    }
    
    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function query($statement) {
    	return self::$PDOInstance->query($statement);
    }
    
    /**
     * Execute query and return all rows in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchAllAssoc($statement) {
    	return self::$PDOInstance->query($statement)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Execute query and return one row in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchRowAssoc($statement) {
    	return self::$PDOInstance->query($statement)->fetch(PDO::FETCH_ASSOC);    	
    }
    
    /**
     * Execute query and select one column only 
     *
     * @param string $statement
     * @return mixed
     */
    public function queryFetchColAssoc($statement) {
    	return self::$PDOInstance->query($statement)->fetchColumn();    	
    }
    
    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quote ($input, $parameter_type=0) {
    	return self::$PDOInstance->quote($input, $parameter_type);
    }
    
    /**
     * Rolls back a transaction
     *
     * @return bool
     */
    public function rollBack() {
    	return self::$PDOInstance->rollBack();
    }      
   
    /**
     * Set an attribute
     *
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value  ) {
    	return self::$PDOInstance->setAttribute($attribute, $value);
    }
}
?>