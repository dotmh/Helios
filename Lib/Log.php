<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 27/03/2014
 * Time: 12:10
 */

namespace Helios\Lib;

use Helios\Lib\Console;

class Log {

    /* Error Levels */
    const DEBUG     = 0;
    const WARNING   = 1;
    const ERROR     = 2;
    const FATAL     = 4;

    /* Aliases */
    const WARN      = self::WARNING;
    const LOG       = self::DEBUG;
    const MSG       = self::DEBUG;

    /* Modes */
    const CLI       = "cli";
    const FILE      = "file";
    const BOTH      = "both";

    /* Default Mode */
    const MODE     = self::CLI;

    /* L18n Default lang */
    const LANG     = "en";

    /* Message Template */
    const CLI_MESSAGE = "{{level}} {{message}}";
    const FILE_MESSAGE = "{{level}} at [{{time}}] - {{message}}";
    const TIME         = "Y-m-d@H:i:s";

    /* Colors */
    static private $colors = [
      self::DEBUG => Console::BRIGHTBLUE,
      self::WARNING => Console::YELLOW,
      self::ERROR   => Console::RED,
      self::FATAL   => [Console::BLACK , Console::RED]
    ];

    /* L18n translation map */
    static private $levels = [
        "en" => [
            self::DEBUG => "Debug",
            self::WARNING => "Warning",
            self::ERROR  => "Error",
            self::FATAL => "!FATAL!"
        ]
    ];

    /* The default file name to log out to */
    static public $logfile = null;

    /* The min level to log out at */
    static public $log_at = self::DEBUG;


    public static function __callStatic($name , $arguments) {
        $level = defined("self::".strtoupper($name)) ? constant("self::".strtoupper($name)) : self::DEBUG;
        $message = implode(" , " , $arguments);
        self::out($level , $message);
    }

    /**
     * Log out to the required outputs
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $message the message text
     * @param int $level the message level i.e. debug
     */
    public static function out($level , $message) {
        if (!defined("self::".strtoupper($level)) ) {
            $level = self::DEBUG;
        }

        if ( $level < self::$log_at ) return;

        switch(self::MODE) {
            case self::FILE:
                self::filePuts($level, $message);
                break;
            case self::BOTH:
                self::filePuts($level, $message);
                self::puts($level, $message);
                break;
            case self::CLI:
            default:
                self::puts($level , $message);
        }
    }

    /**
     * Logs out to the screen output (CLI)
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $message the message text
     * @param int $level the message level i.e. debug
     */
    private static function puts($level , $message) {

        $levelColors = self::$colors[$level];
        $foreground = is_array($levelColors) ? $levelColors[0] : $levelColors;
        $background = is_array($levelColors) ? $levelColors[1] : null;

        Console::say(self::expand($message , $level, self::CLI), $foreground , $background );
    }

    /**
     * Logs out to the file output
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $message the message text
     * @param int $level the message level i.e. debug
     */
    private static function filePuts($level , $message) {
        if ( is_null(self::$logfile) ) {
            self::$logfile = Helpers::file_join(ROOT , "logs", "helios.log");
        }

        $directory = dirname(self::$logfile);

        if ( !file_exists($directory) || !is_dir($directory) ) {
            mkdir($directory , 0777, true);
        }


        file_put_contents(self::$logfile , self::expand($message, $level, self::FILE));

    }

    /**
     * Expands a message out using a template , and the message type
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $message the message text
     * @param int $level the message level i.e. debug
     * @param string $type the type of output the message will be used for
     * @return string the expanded message
     */
    private static function expand($message, $level, $type) {
        $vars = [
            "message" => $message,
            "level"   => self::$levels[self::LANG][$level],
            "time"    => date(self::TIME)
        ];

        switch ($type) {
            case self::CLI:
                $string = Helpers::simple_template(self::CLI_MESSAGE , $vars);
                break;
            case self::FILE:
            default:
                $string = Helpers::simple_template(self::FILE_MESSAGE , $vars);
        }

        return $string;
    }

} 