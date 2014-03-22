<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 21/03/2014
 * Time: 11:57
 */

namespace Helios\Lib;

use Helios\Lib\Exceptions\ConsoleException;

/**
 * Class Console
 * A wrapper class to carry out basic opperations on the terminal mainly for coloring text and handling user inputs
 *
 * @package Helios\Lib
 * @author Martin Haynes
 */
final class Console {

    // Color Control character numbers
    const BLACK       = 0;
    const DARKRED     = 1;
    const GREEN       = 2;
    const DARKYELLOW  = 3;
    const DARKBLUE    = 4;
    const MAGENTA     = 5;
    const CYAN        = 6;
    const SILVER      = 7;
    const GRAY        = '0;1';
    const RED         = '1;1';
    const LIME        = '2;1';
    const YELLOW      = '3;1';
    const BLUE        = '4;1';
    const PINK        = '5;1';
    const BRIGHTBLUE  = '6;1';
    const WHITE       = '7;1';

    /*
     * The extra control string needed to wrap the colors
     * i.e. full string for red text would be \x1b[31;1m
     */
    const CONTROL_PREFIX    = '\x1b[';  // The control prefix
    const CONTROL_SUFFEX    = 'm';      // the control suffix
    const CANCEL_COLOR      = '0';      // The code in order to cancel the color
    const FORGROUND         = 3;        // The number to tell the console we want to target the text color
    const BACKGROUND        = 4;        // The number to tell the console we want to target the background color

    /*
     * Strings that mean yes and no , for the confirm method
     */
    public static $answers = array(
        'yes' => true,
        'y'   => true,
        'n'   => false,
        'no'  => false
    );

    /*
     * Used when testing to fake a line input , this may need to be done another way in the future.
     */
    public static $linein = null;

    /**
     * Outputs text to the console optionally in colors on a background , uses puts to output the text
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the text you wish to print out to the console
     * @param null|string $in_color the color of the text you wish to print must match class constants for colors
     * @param null|string $on_color the color of the background of the text you wish to print
     *  must match class color constants
     * @throws Exceptions\ConsoleException
     */
    final public static function say($text , $in_color = null , $on_color = null) {
        if ( is_null($in_color) && is_null($on_color) ) {
            self::puts($text);
        } elseif ( !is_null($in_color) && is_null($on_color)) {
            self::puts_with_forground($text , $in_color);
        } elseif ( !is_null($in_color) && !is_null($on_color)) {
            self::puts_with_background($text , $in_color , $on_color);
        } else {
            throw new ConsoleException("Banes console doesn't support background without forground");
        }
    }

    /**
     * Prompts the user to enter the answer to a question such as what day is it
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the body of the question that you wish to ask
     * @param null|string $default the default answer , if the user just presses enter
     * @param null|string $in_color the text color to print the question in must match the class constants for colors
     * @param null|string $on_color the background color to print the question in must match the
     *  class constants for colors
     * @return null|string returns the answer supplied as a string
     */
    final public static function ask($text , $default = null, $in_color = null , $on_color = null) {
        if ( !is_null($default) ) $text .= " [".$default."]";
        self::say($text , $in_color , $on_color);
        try {
            return self::readinput($default);
        } catch(ConsoleException $e) {
            self::say("You must answer the above to continue" , self::RED);
            self::ask($text , $default, $in_color , $on_color);
        }
    }

    /**
     * Prompts the user to answer a boolean question (i.e. yes or no)
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $question the question to which you want a yes/no answer
     * @param null|string $default the default answer Y or N
     * @return null|Boolean true on yes , false on no
     */
    final public static function confirm($question , $default = null, $in_color = null , $on_color = null) {
        if ( !is_null($default) ) $question .= " [".($default ? 'Y' : 'N'."]");
        self::say($question , $in_color , $on_color);
        try {
            $answer = self::readinput($default);
            if ( is_bool($answer) ) return $answer;
            if ( !array_key_exists($answer, self::$answers)) {
                self::say("Please answer with ".implode("|", array_keys(self::$answers)) , self::RED);
                self::confirm($question , $default, $in_color , $on_color);
            }

            return self::$answers[$answer];

        } catch (ConsoleException $e) {
            self::say('You must answer the above to continue' , self::RED);
            self::confirm($question , $default, $in_color , $on_color);
        }

    }

    /**
     * Underlines the text with the correct number of charaters
     *
     * @param string $text the text you wish to underline
     * @param null|string $in_color the color for the forground
     * @param null|string $on_color the color for the background
     * @param string $with (-) the character to use for the underline
     */
    final public static function underline($text , $in_color = null , $on_color = null, $with = "-") {
        $length = strlen($text);
        $underline = "";
        for ( $_i = 0; $_i < $length; ++$_i) {
            $underline .= $with;
        }

        self::say($text, $in_color , $on_color);
        self::say($underline , $in_color , $on_color);

    }

    /**
     * Out put text to the terminal __WITHOUT__ a line break in color , resets the color
     *
     * @todo review access level for this method it may need to be made private
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the text to output to the terminal
     * @param string $control the control characters to output
     */
    final public static function outs($text , $control = '') {
        self::reset_color();
        echo $control.$text;
    }

    /**
     * Puts is a ruby method meaning output to terminal with a line break , which is the same here except this can
     * have a forground color set
     *
     * @see self::puts
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the text to put to the terminal
     * @param string $forground the color to output the text in , must match the class constants
     */
    final public static function puts_with_forground($text , $forground) {
        self::outs($text.PHP_EOL, self::color($forground));
    }

    /**
     * Puts the text to the terminal with a background and foreground color
     *
     * @see self::puts
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the text to put to the terminal
     * @param string $forground the color to output the text in , must match the class constants
     * @param string $background the color to output the text background in , must match the class constants
     */
    final public static function puts_with_background($text , $forground, $background) {
        self::outs($text.PHP_EOL, self::color($forground).self::color($background , true));
    }

    /**
     * Taken from Ruby puts means output text to the terminal with a linebreak , in this case we make sure we use
     * the correct line break for the system by using the PHP constant PHP_EOL rather than manually adding a control
     * character ourselves
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $text the text to put to the terminal
     */
    final public static function puts($text) {
        self::outs($text.PHP_EOL);
    }

    /**
     * Use to read the users input value from the terminal , does some basic validation
     *
     * @param null|string $default the default value to use if the user does not enter anything
     * @return null|string the valus the user entered or default if no value is entered and default is supplied
     * @throws Exceptions\ConsoleException throws this is the default is null and no answer was supplied i.e
     *  the answer is required!
     */
    final public static function readinput($default = null) {
        if ( !is_null(self::$linein)) {
            $linein  = self::$linein;
        } else {
            $linein = strtolower(trim(fgets(STDIN)));
        }
        if ( empty($linein) && !is_null($default)) {
            return $default;
        } elseif ( empty($linein) && is_null($default)) {
            throw new ConsoleException("you are required to provide an answer");
        } else {
            return $linein;
        }
    }

    /**
     * Builds the control string to send to the terminal to change the colors
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     *
     * @param string $color the color control character i.e. 1;1 for red
     * @param bool $background whether this is a background color as extra control chars are needed
     * @return string the control character string to use
     */
    final private static function color($color , $background = false) {
        return self::CONTROL_PREFIX.($background ? self::BACKGROUND : self::FORGROUND).$color.self::CONTROL_SUFFEX;
    }

    /**
     * Resets the terminal colors back to there defaults i.e. clears all colors set before it is called
     */
    final public static function reset_color() {
        echo self::CONTROL_PREFIX.self::CANCEL_COLOR.self::CONTROL_SUFFEX;
    }

    /**
     * Gets a the color constant form a color string
     *
     * @example
     *  color("red") //=> 1;1 which is the color control character for red
     *
     * @package Helios\Lib\Console
     * @author Martin Haynes <oss@dotmh.com>
     *
     * @param string $string the string value of the constant you wish to get the value off
     * @return mixed the constant value for that color
     * @throws Exceptions\ConsoleException
     */
    final private static function string_to_color($string) {
        $constant = __CLASS__.'::'.strtoupper($string);
        if ( defined($constant) ) {
            return constant($constant);
        } else {
            throw new ConsoleException('can not find a color '.$string);
        }
    }

    final public static function __callStatic($name , $argumens) {
        $parts = explode('_', $name);
        if ( method_exists(__CLASS__, $parts[0])) {

            $forground = (isset($parts[2])) ? self::string_to_color($parts[2]) : null;
            $background = (isset($parts[4])) ? self::string_to_color($parts[3]) : null;
            $function = $parts[0];

            self::$function($argumens[0], $forground , $background);
        } else {
            throw new ConsoleException('can not find the method '.$parts[0]);
        }
    }

}