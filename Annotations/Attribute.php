<?php
/**
 * Attribute class
 */

namespace App\Annotations;

use App\Annotations\Annotation;

/**
 * This class represents an annotation for a column in a database's table
 */
class Attribute extends Annotation {
    
    /**
     * The type of the data stored in the column
     *
     * @var string
     */
    protected $type;

    /**
     * The length of the data stored in the column
     *
     * @var int
     */
    protected $length;

    /**
     * The default value of the data stored in the column
     *
     * @var string
     */
    protected $default;

    /**
     * Is the column allowed to be null ?
     *
     * @var bool
     */
    protected $not_null;

    /**
     * Is the column data unique ?
     *
     * @var bool
     */
    protected $unique;

    /**
     * Is the column the primary key ?
     *
     * @var bool
     */
    protected $primary;

    /**
     * Does the column autoincrement ?
     *
     * @var bool
     */
    protected $auto_increment;

    /**
     * The Entity class corresponding to the column
     *
     * @var string
     */
    protected $foreign;

    /**
     * Constructor - Sets the properties to default values
     *
     * @return void
     */
    public function __construct() {
        $this->annotation_type = 'Attribute';
        $this->type = 'varchar';
        $this->length = 0;
        $this->default = NULL;
        $this->primary = false;
        $this->auto_increment = false;
        $this->foreign = false;
        $this->not_null = false;
        $this->unique = false;
    }
}