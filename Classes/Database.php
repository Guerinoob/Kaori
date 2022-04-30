<?php
/**
 * Database class
 */

namespace App;

use PDO;

/**
 * This class handles the database connection and can be used to perform queries
 */
class Database {    
    /**
     * The database instance
     * 
     * @var Database
     */
    private static $database;

    /**
     * The pdo instance
     *
     * @var \PDO
     */
    public $pdo;
    
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

        $this->pdo = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $db_user, $db_password, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"']);
    }

    /**
     * Returns the Database instance
     *
     * @return Database The database instance
     */
    public static function getInstance(): Database
    {
        if(static::$database)
            return static::$database;

        static::$database = new Database(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);
        return static::$database;
    }
    
    /**
     * Returns the pdo instance
     *
     * @return PDO The pdo instance
     */
    public function getPDOInstance(): \PDO
    {
        return $this->pdo;
    }
    
    /**
     * Returns the ID of the last inserted row
     *
     * @return int The ID of the last inserted row
     */
    public function getLastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Performs a select request and returns the fetched result, or false if there was an error
     *
     * @param  string $query The SQL select query
     * @return false|array Returns false if an error occured, else an associative array 
     */
    public function select($query)
    {
        if(!$this->pdo)
            return false;

        if(!preg_match('/^\s*select\s/i', $query))
            return false;

        $statement = $this->pdo->query($query);

        if(!$statement)
            return false;

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Prepares a request, making it safe towards injections
     *
     * @param  string $query The query to prepare
     * @return false|PDOStatement Returns false if an error occured, else the statement object
     */
    public function prepare($query)
    {
        $prepared_stmt = $this->pdo->prepare($query);

        if(!$prepared_stmt) return false;

        return $prepared_stmt;
    }
    
    /**
     * Executes a prepared query through its statement
     *
     * @param  array $args The parameters of the prepared query
     * @param  PDOStatement $statement The statement object of the prepared query
     * @return bool|array Returns false if an error occured, true if the query was performed without returning a result (UPDATE, INSERT, CREATE TABLE...), or an array of results if it was a SELECT
     */
    public function execute_prepared_query($args, $statement)
    {
        if(!$statement->execute($args)) return false;

        //Return results if it has some (for select queries)
        if(preg_match('/^\s*select\s/i', $statement->queryString)) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        return true;

    }
    
    /**
     * Shortcut for prepare and execute_prepared_query methods
     *
     * @see Database::prepare()
     * @see Database::execute_prepared_query()
     * 
     * @param  mixed $query The query to execute
     * @param  mixed $args The arguments for the query
     * @return bool|array
     */
    public function query($query, $args)
    {
        if(!($statement = $this->prepare($query)))
            return false;

        return $this->execute_prepared_query($args, $statement);
    }
}