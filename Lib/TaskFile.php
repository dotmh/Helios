<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 27/03/2014
 * Time: 12:01
 */

namespace Helios\Lib;

final class TaskFile {

    public $filename = "";

    private static $filenames = [
        "taskfile.php",
        "tasks.php",
        "helios.php",
        "heliosTasks.php"
    ];

    private static $defaultFilename = "DefaultTaks.php";



    final public function __construct() {

    }

    final private function searchPath() {
        return \getcwd();
    }

    final private function discover() {
        foreach ( self::$filenames as $filename ) {
            if ( file_exists(Helpers::file_join($this->searchPath() , $filename)) ) {
                $this->filename = $filename;
                break;
            }
        }

        if ( empty($filename) ) {

        }

    }


}