<?php
/**
 * Annotation class
 */

namespace App\Annotations;

/**
 * This class is the parent of every annotation class. An annotation is a doc comment line starting with the symbol "@"
 */
abstract class Annotation {    
    /**
     * The type of the annotation (basically, the word following the "@" symbol)
     *
     * @var mixed
     */
    protected $annotation_type;
    
    /**
     * Returns the annotation type
     *
     * @return string The annotation type
     */
    public function getAnnotationType(): string
    {
        return $this->annotation_type;
    }
    
    /**
     * Sets the value of a property
     *
     * @param  string $key The property name
     * @param  mixed $value The value
     * @return bool True if the property exists, else if it doesn't
     */
    public function set($key, $value): bool 
    {
        if(property_exists($this, $key)) {
            $this->$key = $value;
            return true;
        }

        return false;
    }
    
    /**
     * Returns the value of a property
     *
     * @param  string $key The property name
     * @return mixed|null Returns the property value if the property exists, null if it doesn't
     */
    public function get($key)
    {
        if(property_exists($this, $key)) {
            return $this->$key;
        }

        return null;
    }

}