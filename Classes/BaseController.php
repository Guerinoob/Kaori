<?php
/**
 * BaseController class
 */

namespace App;

use App\Annotations\AnnotationParser;

/**
 * This class serves as the parent of all controllers classes
 */
class BaseController {
    
    /**
     * Constructor - Every controller that is instanciated has all its methods annotations parsed in order to add the routes to the global router
     *
     * @return void
     */
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