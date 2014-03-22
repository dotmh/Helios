<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 21/03/2014
 * Time: 12:59
 */

namespace Helios;

require_once "Lib/Autoloader.php";

use \Helios\Lib\Loader;

final class Main {
    private $loader;
    final public function __construct() {
        if ( !defined('BASE_PATH') ) define('BASE_PATH', __DIR__);
        $this->loader = new Loader();
    }
}

$helios = new Main();
