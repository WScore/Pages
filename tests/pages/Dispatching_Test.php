<?php
namespace tests\pages;

use tests\pages\mocks\TestController;
use WScore\Pages\Dispatch;
use WScore\Pages\Factory;
use WScore\Pages\PageView;

require_once( dirname(__DIR__).'/autoload.php' );
require_once( __DIR__ . '/mocks/TestController.php' );

class Dispatching_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestController
     */
    var $c;

    /**
     * @var Dispatch
     */
    var $d;
    
    function setup()
    {
        $this->c = new TestController();
        $this->d = Factory::getDispatch( $this->c );
    }
    
    function test0()
    {
        $this->assertEquals('tests\pages\mocks\TestController', get_class($this->c));
        $this->assertEquals('WScore\Pages\Dispatch', get_class($this->d));
    }
}