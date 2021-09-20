<?php

namespace App\Annotations;

use App\Annotations\Annotation;

class Route extends Annotation {

    protected $path;

    protected $callback;

    protected $methods;

    public function __construct() {
        $this->annotation_type = 'Route';
        $this->path = '';
        $this->callback = null;
        $this->methods = [];
    }
}