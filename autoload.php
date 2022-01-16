<?php
spl_autoload_register(function ($class_name) {
    if(strpos($class_name, '\\') >= 0) { //Use statement
        $array = explode('\\', $class_name);
        array_shift($array); //Removes the "App"

        $path = DOCUMENT_ROOT.'/';
        foreach($array as $i => $element) {
            $path .= $element;
            if(count($array) > $i+1)
                $path .= '/';
        }

        $path .= '.php';

        if(file_exists($path)) {
            require_once $path;
        }
        else {
            require_once DOCUMENT_ROOT.'/Classes/'.implode('/', $array).'.php';
        }
    }
    else {
        require_once $class_name . '.php';
    }
});