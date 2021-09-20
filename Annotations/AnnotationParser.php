<?php

namespace App\Annotations;

class AnnotationParser {
    public static function parse($docComment) {
        $docComment = str_replace('/*', '', $docComment);
        $docComment = str_replace('*/', '', $docComment);
        preg_match_all("/\*(.*)\n/m", $docComment, $lines);
        
        array_shift($lines);

        $lines = $lines[0];
        array_shift($lines);

        $annotations = [];

        foreach($lines as $line) {
            preg_match_all("/@(.*)\(/", $line, $array);
            array_shift($array);

            $type = $array[0][0];

            preg_match_all("/\((.*)\)/", $line, $array);
            array_shift($array);


            $data = $array[0][0];

            $data = str_replace('=', ':', $data);

            $data = '{'.$data.'}';

            $data = str_replace("\\", "\\\\", $data);

            $data = preg_replace("/\{([^:]*):/", '{"$1":', $data);
            $data = preg_replace("/, ([^:]*):/", ',"$1":', $data);

            $data = str_replace('"{', '{', $data);
            $data = str_replace('}"', '}', $data);


            $data = json_decode($data, true);


            $class = 'App\\Annotations\\'.$type;

            if(!class_exists($class)) 
                return false;

            $annotation = new $class;

            foreach($data as $key => $attr)
                $annotation->set($key, $attr);

            $annotations[] = $annotation;
        }

        return $annotations;

    }
}