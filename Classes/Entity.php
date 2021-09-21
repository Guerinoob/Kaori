<?php
/**
 * Entity class
 */

 namespace App;

 use App\Annotations\AnnotationParser;
 
 /**
  * This class represents an Entity stored in the database. Each column is declared with a @Attribute Annotation.
  * This class implements the basic methods of an entity (load, save, delete...). It can not be instantiated
  *
  * @see Annotation
  */
 abstract class Entity {
     /**
     * The ID of the entity in the database
     * @Attribute(type="int", length=11, primary=true, auto_increment=true, not_null=true)
     * 
     * @var int
     */
    protected $id;

    /**
     * The name of the table stored in the database
     * 
     * @var string
     */
    public static $table;
    
    /**
     * Constructor - Loads the entity from the database if an ID is provided
     *
     * @param  int $id ID of the entity, null if it's a new entity
     * @return void
     */
    public function __construct($id = null)
    {
        if($id !== null)
            $this->loadById($id);
    }
    
    /**
     * Returns the ID of the entity
     *
     * @return int The ID of the entity
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * Saves the entity in the database. Inserts it if it's a new entity, or updates it if it exists
     *
     * @return bool True if the entity has been saved, false if an error occured
     */
    public function save(): bool
    {
        global $db;

        $attributes = get_class_vars($this->getClassName());
        $exclude = array_fill_keys(['table', 'id'], ''); //Properties that are not in the database

        $attributes = array_diff_key($attributes, $exclude);

        if($this->id === null) {
            //It's a new entity, so it will be an INSERT query
            $query = "INSERT INTO ".$this->getClassName()::$table."(";

            $field_names = implode(', ', array_keys($attributes));

            $field_values = implode(', ', array_fill(0, count($attributes), '?'));

            $parameters = array_values($attributes);

            $query .= $field_names.") VALUES(".$field_values.")";

            if(!$db->query($query, $parameters))
                return false;

            $this->id = $db->getLastInsertedId();
            return true;
        }
        else {
            //The entity already exists, so it will be an UPDATE query
            $query = "UPDATE ".$this->getClassName()::$table." SET ";

            $fields = implode(' = ?, ', array_keys($attributes));

            $parameters = array_values($attributes);
            $parameters[] = $this->id;

            $query .= $fields." WHERE id = ?";

            return $db->query($query, $parameters);
        }
    }
    
    /**
     * Deletes the entity from the database
     *
     * @return bool True if the entity has been deleted, false if it has not
     */
    public function delete(): bool
    {
        global $db;

        if(!$db->query("DELETE FROM ".$this->getClassName()::$table." WHERE id = ?", [$this->id]))
            return false;

        $this->id = null;
        return true;
    }
    
    /**
     * Creates the table corresponding to this entity in the database (without the foreign constraints)
     * To add the constraints, see the addConstraints method
     * 
     * @see Entity::addConstraints()
     *
     * @return bool Returns true if the table was created, false if an error occured
     */
    public static function createTable(): bool
    {
        global $db;

        $class_name = get_called_class();

        $query = "CREATE TABLE IF NOT EXISTS ".$class_name::$table." (";

        $reflection = new \ReflectionClass($class_name);

        $lengthable_types = ['VARCHAR', 'INT', 'TEXT'];

        foreach($reflection->getProperties() as $property) {
            $docComment = $property->getDocComment();
            
            if($docComment) {
                $annotations = AnnotationParser::parse($docComment);

                foreach($annotations as $annotation) {
                    if($annotation->getAnnotationType() == 'Attribute') {
                        $line = $property->getName().' '.strtoupper($annotation->get('type'));

                        if($annotation->get('length') && in_array(strtoupper($annotation->get('type')), $lengthable_types)) {
                            $line .= '('.$annotation->get('length').')';
                        }

                        if($annotation->get('unique')) {
                            $line .= ' UNIQUE';
                        }

                        if($annotation->get('not_null')) {
                            $line .= ' NOT NULL';
                        }

                        if($annotation->get('default')) {
                            $default = $annotation->get('default');

                            if(strtoupper($annotation->get('type')) == 'DATE') {
                                $default = date('Y-m-d', strtotime($default));
                            }
                            else if(strtoupper($annotation->get('type')) == 'DATETIME') {
                                $default = date('Y-m-d H:i:s', strtotime($default));
                            }

                            $line .= ' DEFAULT \''.$default.'\'';
                        }

                        if($annotation->get('auto_increment')) {
                            $line .= ' AUTO_INCREMENT';
                        }
                        
                        if($annotation->get('primary')) {
                            $line .= ' PRIMARY KEY';
                        }

                        /* if($annotation->get('foreign')) {
                            $class = $annotation->get('foreign');
                            $table = $class::$table;
                            $constraints .= 'CONSTRAINT FK_'.$table.'_'.$class_name::$table.'_'.$property->getName().' FOREIGN KEY ('.$property->getName().') REFERENCES '.$table.'(id), ';
                        } */

                        $line .= ', ';

                        $query .= $line;
                    }
                }

            }
        }

        $query = substr($query, 0, strlen($query)-2);

        $query .= ")";

        return $db->query($query, []);
    }
    
    /**
     * Adds the constraints to the table of the entity
     *
     * @return bool Returns true if successful, false if an error occured
     */
    public static function addConstraints(): bool
    {
        global $db;

        $class_name = get_called_class();

        $query = "ALTER TABLE ".$class_name::$table." ";

        $reflection = new \ReflectionClass($class_name);

        $alter = false;

        foreach($reflection->getProperties() as $property) {
            $docComment = $property->getDocComment();
            
            if($docComment) {
                $annotations = AnnotationParser::parse($docComment);

                foreach($annotations as $annotation) {
                    if($annotation->getAnnotationType() == 'Attribute') {

                        if($annotation->get('foreign')) {
                            $alter = true;
                            $class = $annotation->get('foreign');
                            $table = $class::$table;
                            $query .= 'ADD CONSTRAINT FK_'.$table.'_'.$class_name::$table.'_'.$property->getName().' FOREIGN KEY ('.$property->getName().') REFERENCES '.$table.'(id), ';
                        }

                    }
                }

            }
        }

        if(!$alter)
            return true;

        $query = substr($query, 0, strlen($query)-2);

        return $db->query($query, []);
    }
    
    /**
     * Returns a JSON string representing the entity
     *
     * @return string A JSON string representing the entity
     */
    public function toJson() {
        return json_encode($this->toStdObject(), JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Returns an object containing the properties of the entity
     *
     * @return object An object containing the properties of the entity
     */
    public function toStdObject() {
        $obj = new \stdClass();

        $attributes = get_class_vars($this->class_name);
        $exclude = ['table', 'class_name'];

        foreach($attributes as $key => $default_value) {
            if(!in_array($key, $exclude))
                $obj->$key = $this->$key;
        }

        $obj->toString = $this->__toString();

        return $obj;
    }
    
    /**
     * Returns a string describing or representing the entity
     *
     * @return string A string describing or representing the entity
     */
    public function __toString() {
        return '';
    }

    
    /**
     * Returns the name of the instantiated class
     *
     * @return string The name of the instantiated class
     */
    protected function getClassName(): string
    {
        return get_class($this);
    }
    
    /**
     * Loads the entity corresponding to the ID from the database
     *
     * @param  mixed $id The ID of the entity
     * @return void
     */
    private function loadById($id): void 
    {
        global $db;
        
        $result = $db->query("SELECT * FROM ".$this->getClassName()::$table." WHERE id = ?", [$id]);

        if($result)
            $this->load($result[0]);
    }
    
    /**
     * Sets all the properties of the entity
     *
     * @param  array $data An associative array of data to set
     * @return void
     */
    private function load($data): void 
    {
        $attributes = get_class_vars($this->getClassName());

        foreach($attributes as $key => $default_value) {
            if(is_array($data) && isset($data[$key])) {
                $this->$key = htmlentities(html_entity_decode($data[$key]), ENT_NOQUOTES);
            }
            else if(is_object($data) && isset($data->$key)) {
                $this->$key = htmlentities(html_entity_decode($data->$key), ENT_NOQUOTES);
            }

        }
    }

 }