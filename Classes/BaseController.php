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

    private $renderer;
    
    /**
     * Constructor - Every controller that is instanciated has all its methods annotations parsed in order to add the routes to the global router
     *
     * @return void
     */
    public function __construct() {
        $this->renderer = new Renderer();

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
    
    /**
     * Shortcut for Renderer::view()
     * 
     * @see Renderer::view()
     *
     * @param  mixed $variable Name of the variable in the template
     * @param  mixed $value Value of the variable
     * @return void
     */
    protected function assign($variable, $value) 
    {
        $this->renderer->assign($variable, $value);
    }
    
    /**
     * Shortcut for Renderer::render()
     * 
     * @see Renderer::render()
     *
     * @param  mixed $path Path to the template
     * @return void
     */
    protected function render($path)
    {
        $this->renderer->renderView($path);
    }
    
    /**
     * Shortcut for Renderer::addJs()
     * 
     * @see Renderer::addJs()
     *
     * @param  mixed $path Path to the script file
     * @return void
     */
    protected function addJs($path) 
    {
        $this->renderer->addJs($path);
    }
    
    /**
     * Shortcut for Renderer::addCss()
     * 
     * @see Renderer::addCss()
     *
     * @param  mixed $path Path to the stylesheet file
     * @return void
     */
    protected function addCss($path)
    {
        $this->renderer->addCss($path);
    }


}