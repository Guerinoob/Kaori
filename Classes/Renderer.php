<?php
/**
 * Renderer class
 */

namespace App;

/**
 * This class is used in controllers to add resources (JS, CSS), variables and render a template including these data.
 */
class Renderer {
    
    /**
     * All the variables that can be used in the rendered template
     *
     * @var array
     */
    private $data = [];

    /**
     * Stores JS scripts included from a template or a class used in a template, in order to not include them twice
     *
     * @var array
     */
    private static $scripts = [];
    
    /**
     * Constructor - Adds basics resources to the renderer
     *
     * @return void
     */
    public function __construct()
    {
        if(file_exists(ROOT_THEME_URL.'/assets/js/main.js'))
            $this->addJS(ROOT_THEME_URL.'/assets/js/main.js');
    }
    
    /**
     * Renders a template, and extracts the assigned data to use them in the template
     *
     * @param  string $path Path to the template
     * @return void
     */
    public function renderView($path)
    {
        extract($this->data);
        require $path;
    }
    
    /**
     * Same as renderView, but the content of the template is returned instead of printed
     *
     * @param  string $path Path to the template
     * @return string The content of the template
     */
    public function getTemplate($path): string
    {
        extract($this->data);
        
        ob_start();
        require $path;
        return ob_get_clean();
    }
    
    /**
     * Adds a script to the renderer, that will be printed when the template will be rendered
     *
     * @param  mixed $path Path to the script file
     * @return void
     */
    public function addJs($path) 
    {
        if(!isset($this->data['js_scripts']))
            $this->data['js_scripts'] = [];

        $this->data['js_scripts'][] = $path;
    }

    /**
     * Adds a stylesheet to the renderer, that will be printed when the template will be rendered
     *
     * @param  mixed $path Path to the stylesheet file
     * @return void
     */
    public function addCss($path) 
    {
        if(!isset($this->data['css_scripts']))
            $this->data['css_scripts'] = [];

        $this->data['css_scripts'][] = $path;
    }
    
    /**
     * Adds a variable to the renderer, that will be printed when the template will be rendered
     *
     * @param  mixed $variable Name of the variable in the template
     * @param  mixed $value Value of the variable
     * @return void
     */
    public function assign($variable, $value) 
    {
        $this->data[$variable] = $value;
    }
    
    /**
     * Returns the value of the variable stored in the renderer
     *
     * @param  string $key The name of the desired variable
     * @return mixed Returns the value of the variable or null if it doesn't exist
     */
    public function getVariable($key)
    {
        return $this->data[$key] ?? null;
    }
    
    /**
     * Prints a script tag with the given URL
     *
     * @param  mixed $url The URL of the script file
     * @return void
     */
    public static function printJs($url) 
    {
        if(!in_array($url, self::$scripts)) {
            echo '<script src="'.$url.'"></script>';
            self::$scripts[] = $url;
        }
    }

}
