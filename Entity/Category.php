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
}