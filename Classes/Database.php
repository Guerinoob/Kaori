<?php
/**
 * Database class
 */

/**
 * This class handles the database connection and can be used to perform queries
 */
class Database {    
    /**
     * The mysqli instance
     *
     * @var mysqli
     */
    public $mysqli_instance;
    
    /**
     * The database user
     *
     * @var string
     */
    private $db_user;

    /**
     * The database password
     *
     * @var string
     */
    private $db_passwword;

    /**
     * The database host
     *
     * @var string
     */
    private $db_host;

    /**
     * The database name
     *
     * @var string
     */
    private $db_name;

        
    /**
     * Constructor - Connects to the database
     *
     * @param  string $db_user Database user
     * @param  string $db_password Database password
     * @param  string $db_host Database host
     * @param  string $db_name Database name
     * @return void
     */
    public function __construct($db_user, $db_password, $db_host, $db_name)
    {
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->db_name = $db_name;

        $this->mysqli_instance = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        mysqli_query($this->mysqli_instance, "SET NAMES utf8");
    }
    
    /**
     * Returns the mysqli instance
     *
     * @return mysqli The database instance
     */
    public function getInstance(): mysqli
    {
        return $this->mysqli_instance;
    }
    
    /**
     * Returns the ID of the last inserted row
     *
     * @return int The ID of the last inserted row
     */
    public function getLastInsertId(): int
    {
        return mysqli_insert_id($this->mysqli_instance);
    }
    
    /**
     * Performs a select request and returns the fetched result, or false if there was an error
     *
     * @param  string $query The SQL select query
     * @return false|array Returns false if an error occured, else an associative array 
     */
    public function select($query)
    {
        if(!$this->mysqli_instance)
            return false;

        if(!preg_match('/^\s*select\s/i', $query))
            return false;

        $statement = mysqli_query($this->mysqli_instance, $query);

        if(!$statement)
            return false;

        return $statement->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Prepares a request, making it safe towards injections
     *
     * @param  string $query The query to prepare
     * @return false|mysqli_stmt Returns false if an error occured, else the statement object
     */
    public function prepare($query)
    {
        $prepared_stmt = mysqli_prepare($this->mysqli_instance, $query);

        if(!$prepared_stmt) return false;

        return $prepared_stmt;
    }
    
    /**
     * Executes a prepared query through its statement
     *
     * @param  array $args The parameters of the prepared query
     * @param  mysqli_stmt $statement The statement object of the prepared query
     * @return bool|array Returns false if an error occured, true if the query was performed without returning a result (UPDATE, INSERT, CREATE TABLE...), or an array of results if it was a SELECT
     */
    public function execute_prepared_query($args, $statement)
    {
        $types = "";
        $array = array();

        foreach ($args as $value){
            switch(gettype($value)){
                case 'integer':
                    $types .= 'i';
                    break;

                case 'string':
                    $types .= 's';
                    break;

                case 'double':
                    $types .= 'd';
                    break;

                default:
                    $types .= 's';
                    break;
            }

            $array[] = $value;
        }

        if(count($array) > 0){
            if(!$statement->bind_param($types, ...$array)) return false;
        }


        if(!$statement->execute()) return false;

        //Return results if it has some (for select queries)
        if(($results = $statement->get_result())){
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        return true;

    }
    
    /**
     * Shortcut for prepare and execute_prepared_query methods
     *
     * @see Database::prepare()
     * @see Database::execute_prepared_query()
     * 
     * @param  mixed $query
     * @param  mixed $args
     * @return bool|array
     */
    public function query($query, $args)
    {
        if(!($statement = $this->prepare($query)))
            return false;

        return $this->execute_prepared_query($args, $statement);
    }
}

//We instantiate the database and make it global in order to be able to access it anywhere with only one instance
global $db;
$db = new Database(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);