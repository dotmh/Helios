<?php
use Codeception\Util\Stub;
use Helios\Lib\Options;

class OptionsTest extends \Codeception\TestCase\Test
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
    /**
     * It should create a instance of the class with no errors
     */
    public function testConstruction() {
        $options = new Options([],[],"foo");

        $this->assertInstanceOf("\\Helios\\Lib\\Options" , $options);
    }

    /**
     * It should set default values for mapped options not passed
     */
    public function testLoadingOfDefaults() {
        $options = new Options([
                "foo" => [
                    "bar" => [Options::DEFAULT_VALUE => "bar" , Options::TYPE => "string"]
                ]
            ], [], "foo");

        $this->assertEquals("bar" , $options->foo);
    }

}