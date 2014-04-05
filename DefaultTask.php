<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 27/03/2014
 * Time: 11:57
 */

use Helios\Task;
use Helios\Lib\Composer;
use Helios\Lib\Console;

class DefaultTask extends Task
{
    public function welcome() {
        $composer = new Composer(Helpers::file_join(ROOT , "composer.json"));
        Console::say_yellow("HELIOS");
        Console::rule(Console::YELLOW);
        Console::say_darkyellow($composer->description);
        Console::say_darkyellow("by {$composer->authors()}");
        Console::say_darkyellow("Version {$composer->version}");
        Console::say_darkyellow("License {$composer->license}");
        Console::rule(Console::DARKYELLOW , null, "-");
        Console::clear();
    }
}