<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 24/03/2014
 * Time: 16:35
 */

namespace Helios\Lib;

use Helios\Lib\Exceptions\ComposerException;

final class Composer {

    const AUTHORS = "authors";


    private $data;

    final public function __construct($composerJson) {
        if ( !file_exists($composerJson) ) {
            throw new ComposerException("Can not find the composer file {$composerJson}");
        }

        $raw = file_get_contents($composerJson);

        $this->data = json_decode($raw, true);
    }

    final public function __get($name) {
        return array_key_exists($name , $this->data) ? $this->data[$name] : null;
    }

    final public function authors($template = "{{name}} <{{email}}>") {
        if ( !array_key_exists(self::AUTHORS , $this->data) ) {
            return "";
        }

        $authors = [];

        foreach ($this->data[self::AUTHORS] as $author) {
            array_push($authors , Helpers::simple_template($template , $author));
        }

        return implode(", " , $authors);
    }

}