<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 21/03/2014
 * Time: 12:56
 */

namespace Helios\Lib;

class Loader
{

    public function __construct()
    {
        spl_autoload_register(array($this , 'load'));
    }

    public function load( $class )
    {
        if (\strpos($class, '\\') === FALSE )
        {
            $class = __NAMESPACE__.'\\'.$class;
        }

        $path = str_replace('\\', '/', $class);

        $path = BASE_PATH.'/../'.$path.'.php'; // add the ext and context

        if ( !\file_exists($path) )
        {
            return FALSE;
        }

        require_once($path);
    }
}