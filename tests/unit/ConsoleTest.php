<?php
require realpath(implode([__DIR__ , '..' , '..', 'Helios.php'] , DIRECTORY_SEPARATOR));

use Codeception\Util\Stub;
use Helios\Lib\Console;

class ConsoleTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    /*
     * Say
     * ===
     */

    /**
     * it should add a clear color control string when no args are passed to say
     */
    public function testSay() {
        $expected = "hello world";
        $this->expectOutputString('\x1b[0m'.$expected.PHP_EOL);

        Console::say($expected);
    }

    /**
     * It should add the color control string to the text when a foreground color is passed to say
     */
    public function testSayForeground() {
        $expected = "hello world";
        $this->expectOutputString('\x1b[0m\x1b[31;1m'.$expected.PHP_EOL);

        Console::say($expected , Console::RED);
    }

    /**
     * It should add the color control string to the text when a foreground and background color are passed
     */
    public function testSayForegroundWithBackground(){
        $expected = "hello world";
        $this->expectOutputString('\x1b[0m\x1b[31;1m\x1b[41m'.$expected.PHP_EOL);

        Console::say($expected , Console::RED , Console::DARKRED);
    }

    /*
     * Ask
     * ===
     */

    /**
     * it should ask the user to answer a question
     */
    public function testAsk() {
        $expected = "hello world";
        $this->expectOutputString('\x1b[0m'.$expected.PHP_EOL);

        Console::$linein = "hello";
        Console::ask($expected);
    }

    /**
     * It should display the default option if passed
     */
    public function testAskWithDefault() {
        $expected = "hello world";
        $default = "hello";

        $this->expectOutputString('\x1b[0m'.$expected.' ['.$default.']'.PHP_EOL);

        Console::$linein = "hello";
        Console::ask($expected , $default);
    }

    /*
     * Underline
     * =========
     */

    /**
     * it should underline a text
     */
    public function testUnderline() {
        $text = "hello world";

        $this->expectOutputString('\x1b[0m'.$text.PHP_EOL.'\x1b[0m-----------'.PHP_EOL);

        Console::underline($text);
    }

    /**
     * It should allow you to change the underline character
     */
    public function testUnderlineWithAnotherCharacter() {
        $text = "hello world";

        $this->expectOutputString('\x1b[0m'.$text.PHP_EOL.'\x1b[0m==========='.PHP_EOL);

        Console::underline($text,null,null,"=");
    }
}