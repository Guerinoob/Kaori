<?php

namespace App\Entity;

use App\Entity;

class Category extends Entity {
    /**
     * @Attribute(type="varchar", length=255, not_null=true)
     */
    protected $name;

    public static $table = 'category';

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}