<?php

class Database {
    public $mysqli_instance;

    private $db_user;

    private $db_passwword;

    private $db_host;

    private $db_name;

    public function __construct($db_user, $db_password, $db_host, $db_name)
    {
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->db_name = $db_name;

        $this->mysqli_instance = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        mysqli_query($this->mysqli_instance, "SET NAMES utf8");
    }

    public function getInstance(): mysqli
    {
        return $this->mysqli_instance;
    }

    public function select($query) {
        if(!$this->mysqli_instance)
            return false;

        if(!preg_match('/^\s*select\s/i', $query))
            return false;

        $statement = mysqli_query($this->mysqli_instance, $query);

        if(!$statement)
            return false;

        return $statement->fetch_all(MYSQLI_ASSOC);
    }

    public function prepare($query){
        $prepared_stmt = mysqli_prepare($this->mysqli_instance, $query);

        if(!$prepared_stmt) return false;

        return $prepared_stmt;
    }

    public function execute_prepared_query($args, $statement){
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

        if(($results = $statement->get_result())){
            return $results->fetch_all(MYSQLI_ASSOC);
        }

        return true;


    }
}

global $db;
$db = new Database(DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);