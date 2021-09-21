<?php
/**
 * Route class (Annotation)
 */

namespace App\Annotations;

use App\Annotations\Annotation;

/**
 * This class represents an annotation for a route
 */
class Route extends Annotation {
    
    /**
     * The path of the route
     *
     * @var string
     */
    protected $path;
    
    /**
     * An array of HTTP methods
     *
     * @var array
     */
    protected $methods;
    
    /**
     * Constructor - Sets the properties to default values
     *
     * @return void
     */
    public function __construct() {
        $this->annotation_type = 'Route';
        $this->path = '';
        $this->methods = [];
    }
}