<?php
// This is global bootstrap for autoloading

date_default_timezone_set('Europe/London');

require_once realpath(implode([__DIR__ , ".." , "vendor" , "autoload.php"], DIRECTORY_SEPARATOR));