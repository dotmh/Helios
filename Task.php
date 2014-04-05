<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 22/03/2014
 * Time: 18:01
 */

namespace Helios;

use Helios\Lib\Options;

abstract class Task {
    public $optionsMap = [];
    public $options;
    public $router;

    public function __constructor() {

    }

    public function setOptions(array $options) {
        $this->options = new Options($this->optionsMap , $options , $this->router->command);
    }

    public function welcome() {

    }


}