<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 24/03/2014
 * Time: 09:58
 */

/**
 * script  | taskclass | command  | param1 | param2 | var1
 * ===================================================================
 * helios  | ca        | generate | site   | martin | --output=~/sites
 */

namespace Helios\Lib;

use Helios\Lib\Exceptions\RouteException;

final class Router {

    const TASKCLASS = "Tasks";

    private $taskObject;

    public $taskClass;
    public $command;
    public $params = [];
    public $rawOptions = [];

    /**
     * Parses the arguments given to the script and pull out the required information
     * Sorting it in to the correct places
     */
    public function __construct() {

        global $argc , $argv;

        if ( $argc < 1 ) {
            array_shift($argv); // Strip out the script name its not important

            for($_i = 0; $_i < count($argv); ++$_i) {
                $part = $argv[$_i];

                /*
                 * Handle the vars i.e. anything that includes -- or -
                 */
                if ( strpos($part , '--') || strpos($part , '-') ) {
                    array_push($this->rawOptions , $part);
                    continue;
                }

                /*
                 * Handle the class name (optional)
                 */
                if ( $_i === 0 && class_exists($part) ) {
                    $this->taskClass = $part;
                    $this->taskObject = $this->taskObject();
                    continue;
                }

                /*
                 * Handle the command
                 */
                if ( !isset($this->command)) {
                    if ( method_exists($this->taskObject() , $part) ) {
                        $this->command = $part;
                        continue;
                    }
                }

                /*
                 * Handle the params
                 */
                array_push($this->params, $part);
            }

            // If we are valid here we try and call the command parsed
            // Else we attempt to call usage to show the usage message
            // Else we throw an exception , nothing more we can do here.
            if ( $this->valid() ) {
                $this->taskObject()->setOptions($this->rawOptions);
                $this->taskObject()->router = $this;

                call_user_func_array([$this->taskObject() , $this->command] , $this->params);
            } elseif (is_callable([$this->taskObject() , 'usage'])) {
                $this->taskObject()->usage();
            } else {
                throw new RouteException("Can not call command $this->command or usage");
            }
        }
    }

    /**
     * Returns or Creates and Returns an instance of the task class
     *
     * @package Helios\Lib\Router
     * @author Martin Haynes
     *
     * @return mixed
     * @throws Exceptions\RouteException
     */
    final private function taskObject() {
        if ( !isset($this->taskObject)) {
            $this->taskClass = isset($this->taskClass) ? $this->taskClass :
                defined('TASKCLASS') ? TASKCLASS : self::TASKCLASS;

            if ( !class_exists($this->taskClass) ) {
                throw new RouteException("TaskCLass $this->taskClass doesn't exist or could not be found");
            }

            $this->taskObject = new $this->taskClass();
        }

        return $this->taskObject;
    }

    /**
     * Checks to see if we have a valid route to execute
     *
     * @note currently does this by checking the required fields are set
     *
     * @package Helios\Lib\Router
     * @author Martin Haynes
     *
     * @return bool true is valid , false is not - pretty standard I feel.
     */
    final private function valid() {
        if ( isset($this->taskObject) && isset($this->command)) {
            return true;
        }

        return false;
    }

}