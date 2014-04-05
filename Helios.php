<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 21/03/2014
 * Time: 12:59
 */
namespace Helios;

if (!defined("ROOT") ) define("ROOT" , __DIR__);

require_once realpath(implode([ROOT , "vendor" , "autoload.php"], DIRECTORY_SEPARATOR));

\date_default_timezone_set('Europe/London');

use Helios\Lib\Composer;
use Helios\Lib\Console;
use Helios\Lib\Helpers;
use Helios\Lib\Options;
use Helios\Lib\Router;



