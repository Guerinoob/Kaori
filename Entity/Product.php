<?php

namespace App\Entity;

use App\Entity;

class Product extends Entity {
    /**
     * @Attribute(type="varchar", length=255, not_null=true)
     */
    protected $name;

    /**
     * @Attribute(type="float", not_null=true, default=0)
     */
    protected $price;

    /**
     * @Attribute(type="int", foreign="App\Entity\Category")
     */
    protected $category;

    public static $table = 'product';

    public function __construct($id = null)
    {
        parent::__construct($id);
    }
}