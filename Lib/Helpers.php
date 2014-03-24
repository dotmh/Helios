<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 24/03/2014
 * Time: 16:43
 */

namespace Helios\Lib;

final class Helpers {
    final public static function file_join($parts) {
        if ( !is_array($parts) ) $parts = func_get_args();
        return implode($parts , DIRECTORY_SEPARATOR);
    }

    final public static function simple_template($template , $tags) {
        $openTag = "{{";
        $closeTag = "}}";

        $string = $template;

        foreach($tags as $tag => $value) {
            $string = str_replace("{$openTag}{$tag}{$closeTag}" , $value, $string);
        }

        return $string;
    }
}