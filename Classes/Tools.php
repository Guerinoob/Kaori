<?php
/**
 * Tools class
 */

namespace App;

/**
 * This class contains helpers functions that can be called from anywhere
 */
class Tools {    
    /**
     * Creates the tables for all the entities and adds their constraints
     *
     * @return bool Returns true if everything was successful, else otherwise
     */
    public static function installDatabase(): bool 
    {
        $entities = glob('Entity/*.php');
        $entities = array_map(function($filename) {
            preg_match('/\/(\w+)\.php/', $filename, $matches);
            $class = 'App\\Entity\\'.$matches[1];
            return $class;
        }, $entities);

        $result = true;

        foreach($entities as $entity) {
            if(!$entity::createTable())
                $result = false;
        }

        foreach($entities as $entity) {
            if(!$entity::addConstraints())
                $result = false;
        }

        return $result;
    }
    
    /**
     * Returns a JSON string indicating that the response is successful with the given data
     *
     * @param  mixed $data The data to return
     * @return string|false A JSON string or false if $data can not be parsed
     */
    public static function jsonSuccess($data): string
    {
        return json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Returns a JSON string indicating that the response is not successful with the given data
     *
     * @param  mixed $data The data to return
     * @return string|false A JSON string or false if $data can not be parsed
     */
    public static function jsonError($data)
    {
        return json_encode(['success' => false, 'data' => $data]);
    }
}