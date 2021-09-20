<?php

use App\Annotations\AnnotationParser;

class BaseController {

    public function __construct() {
        $reflection = new \ReflectionClass(get_called_class());
        global $router;

        foreach($reflection->getMethods() as $method) {
            $docComment = $method->getDocComment();

            $annotations = AnnotationParser::parse($docComment);

            foreach($annotations as $annotation) {
                if($annotation->getAnnotationType() == 'Route') {
                    $router->addRoute($annotation->get('path'), $method->getClosure($this), $annotation->get('methods'));

                }
            }
        }
    }

}