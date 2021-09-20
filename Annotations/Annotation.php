<?php

namespace App\Annotations;

class Annotation {
    protected $annotation_type;

    public function __construct() {
        $this->attr = [];
    }

    public function getAnnotationType(): string
    {
        return $this->annotation_type;
    }

    public function set($key, $value): bool 
    {
        if(property_exists($this, $key)) {
            $this->$key = $value;
            return true;
        }

        return false;
    }

    public function get($key)
    {
        if(property_exists($this, $key)) {
            return $this->$key;
        }

        return null;
    }

}