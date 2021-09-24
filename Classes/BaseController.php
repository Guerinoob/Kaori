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

        $this->assign('title', 'Kaori');

        if(isset($_SESSION['data']) && is_array($_SESSION['data'])) {
            $data = $_SESSION['data'];

            foreach($data as $key => $value)
                $this->assign($key, $value);

            unset($_SESSION['data']);
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
    protected function assign($variable, $value) : void
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
    protected function render($path): void
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
    protected function addJs($path): void
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
    protected function addCss($path): void
    {
        $this->renderer->addCss($path);
    }
    
    /**
     * Redirects to the given path
     *
     * @param  mixed $path Path to the desired route
     * @return void
     */
    protected function redirect($path, $data = []): void
    {
        $_SESSION['data'] = $data;
        header('Location: '.ROOT_URL.$path);
        exit;
    }
    
    /**
     * Shortcut for Renderer::getVariable()
     * 
     * @see Renderer::getVariable()
     *
     * @param  string $key The name of the desired variable
     * @return mixed Returns the value of the variable or null if it doesn't exist
     */
    protected function getVariable($key): mixed
    {
        return $this->renderer->getVariable($key);
    }


}