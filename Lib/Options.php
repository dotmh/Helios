<?php
namespace Helios\Lib;


/**
 * Class Options
 * Uses the command line options from the router and the options map from a task class to build an easy to use
 * options API for use in the class
 *
 * @package Helios\Lib
 * @author Martin Haynes <oss@dotmh.com>
 *
 * @see Helios\Lib\Router
 */
final class Options {

    const GLOBALKEY = "global";

    public $valid = true;

    private $command;
    private $optionMap;
    private $values = [];
    private $cachedUse = [];

    /**
     * Starts processing the options and the map
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param array $map
     * @param array $options
     * @param $command
     */
    final public function __construct(array $map , array $options, $command) {
        $this->command = $command;
        $this->optionMap = $map;

        // Set all the values to there defaults
        foreach($this->useMap() as $key => $value) {
            $values[$key] = $value[DEFAULT_VALUE];
        }

        $this->parseOptions($options);

    }

    /**
     * Magic method so that you can do $options->foo in order to get the value of option foo
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $name the option to get
     * @return null|mixed the value of the option[$name] or null if it doesn't existed
     */
    final public function __get($name) {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * Returns the information stored in the map regarding a certain key
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $key the option to get the information regarding
     * @return null|array the details about the option or null if it can not be found.
     */
    final public function option($key) {
        return array_key_exists($key , $this->useMap()) ? $this->mapKey($key) : null;
    }

    /**
     * Parse the options that where added to the command line and turn it into something we can use more easily
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param array $options
     */
    final protected function parseOptions(array $options) {

        for($_i = 0; $_i < count($options); ++$_i) {
            $option = $options[$_i];
            $optionParts = explode('=' , $option);
            $key = $optionParts[0];
            $value = isset($optionParts[1]) ? $optionParts[1] : null;

            // Strip the indicators off
            $key = str_replace(["-" . "--"] , "" , $key);

            // Convert a no key to the key + value
            if(is_null($value) && $this->isBoolKey($key)) {
                $key = str_replace("no-" , '' , strtolower($key));
                $value = false;
            }

            // Convert a shortened key back to its full key i.e. -v => verbose
            $key = strlen($key) == 1 ? $this->shortToLong($key) : $key;

            // Convert an alias back to the original
            $key = array_key_exists($key, $this->useMap()) ? $key :
                 !is_null($this->aliased($key)) ? $this->aliased($key) : $key;

            // If the key has not be registered then we simply set its value to whatever value we got and move on
            if (!array_key_exists($key , $this->useMap())) {
                $this->values[$key] = is_null($value) ? $this->valueOf($key) : $value;
                continue;
            }

            // Now we run some checks against the option map
            // Type
            if ( !is_null($value) && $this->mapKey($key, TYPE) != "boolean" && gettype($value) == $this->mapKey($key, TYPE) ) {
                $this->values[$key] = $value;
            } elseif ($this->mapKey($key, TYPE) == "boolean") {
                $this->values[$key] = $this->valueOf($key);
            } else {
                $this->valid = false;
            }


        }
    }

    /**
     * Converts a short parameter i.e. -v into its long version
     *
     * @todo check the aliases for a short parameters
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $short the short parameter to be converted
     * @return null|string the long parameter on success , null on not found
     */
    final private function shortToLong($short) {

        $long = null;

        foreach($this->useMap() as $key => $value) {
            if ( substr(strtolower($key), 0) == $short) {
                $long = $key;
                break;
            }
        }

        return $long;

    }

    /**
     * Coverts a parameter that is actually an alias back to the original
     *
     * @todo add support for short params
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $aliasKey the alias to be converted
     * @return null|string the long parameter key on success , null on not found
     */
    final private function aliased($aliasKey) {

        $actual = null;

        foreach($this->useMap() as $key => $value ) {
            if ( array_key_exists(ALIAS , $value)) {
                if ( is_array($value[ALIAS]) && in_array($aliasKey , $value[ALIAS])) {
                    $actual = $key;
                    break;
                } elseif($value[ALIAS] == $aliasKey) {
                    $actual = $key;
                    break;
                }
            }
        }

        return $actual;
    }

    /**
     * Converts a boolean switch to true of false i.e. -v = true -V = false
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $key the key to convert
     * @return bool true or false value for that key
     */
    final private function valueOf($key) {
        if ( strlen($key) == 1 ) {
            if ( ctype_upper($key) ) {
                return false;
            } else {
                return true;
            }
        } elseif ( strpos($key, "no") === 0) {
            return false;
        }

        return true;
    }

    /**
     * Checks a key to see if it is actual a boolean switch false value
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @todo review the name of this function make sure its intended behaviour makes sense and is easily understood
     *
     * @param string $key the key to check
     * @return bool true it is a boolean no key , false its not
     */
    final private function isBoolKey($key) {
        if ( strlen($key) == 1 && ctype_upper($key)) return true;
        elseif (strpos($key, "no") === 0) return true;
        return false;
    }

    /**
     * Get a options Map by combining the options map for the Default and Command namespace
     * stores the result in cache (first time) and automatically returns that on every other call
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @return array an options map narrowed done to global + command
     */
    final private function useMap() {
        if ( !isset($this->cachedUse) ) {
            $commandOpt = array_key_exists($this->command , $this->optionMap) ? $this->optionMap[$this->command] : [];
            $defaultOpt = array_key_exists(self::GLOBALKEY , $this->optionMap) ? $this->optionMap[self::GLOBALKEY] : [];

            $this->cachedUse = array_merge($defaultOpt, $commandOpt);
        }

        return $this->cachedUse;
    }

    /**
     * Returns a key on the narrowed options map , or on failure the default
     *
     * @see Helios\Lib\Options::useMap
     *
     * @package Helios\Lib\Options
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $key the key for a certain option
     * @param null|string $subkey the key in the sub tree for that option
     * @param null|mixed $default the default value to use if it can't find the $key/$subkey
     * @return null|mixed the value of the option identified by the $key & $subkey
     */
    final private function mapKey($key , $subkey = null ,$default = null) {
        if ( array_key_exists($key , $this->useMap()) ) {

            $datum = $this->useMap()[$key];

            if ( is_null($subkey) ) return $datum;
            if ( array_key_exists($subkey , $datum) ) return $datum[$subkey];
        }

        return $default;
    }


}