<?php
/**
 * AnnotationParser class
 */

namespace App\Annotations;

/**
 * This class allows the parsing of annotations
 */
class AnnotationParser {    
    /**
     * Parses a doc comment into one or more annotations (the doc comment is multiline)
     *
     * @param  string $docComment The Doc comment
     * @return array An array of Annotations object (from a child class)
     * 
     * @see Annotation
     */
    public static function parse($docComment) {
        $docComment = str_replace('/*', '', $docComment);
        $docComment = str_replace('*/', '', $docComment);
        preg_match_all("/\*(.*)\n/m", $docComment, $lines);
        
        array_shift($lines);

        $lines = $lines[0];
        array_shift($lines);

        $annotations = [];

        foreach($lines as $line) {
            if(!preg_match_all("/@(.*)\(/", $line, $array))
                continue;

            array_shift($array);

            $type = $array[0][0];

            $class = 'App\\Annotations\\'.$type;

            if(!class_exists($class)) 
                continue;

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

            $annotation = new $class;

            foreach($data as $key => $attr)
                $annotation->set($key, $attr);

            $annotations[] = $annotation;
        }

        return $annotations;

    }
}