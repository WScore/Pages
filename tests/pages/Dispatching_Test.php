<?php
namespace tests\pages;

use tests\pages\mocks\TestController;
use WScore\Pages\Dispatch;
use WScore\Pages\Factory;

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
        $this->assertEquals('WScore\Pages\Request', get_class($this->d->getRequest()));
        $this->assertEquals('WScore\Pages\Session', get_class($this->d->getSession()));
        $this->assertEquals('WScore\Pages\PageView', get_class($this->d->getView()));
    }

    /**
     * @test
     */
    function dispatches_onExecute_method_from_argument()
    {
        $view = $this->d->execute('execute');
        $this->assertEquals('executed', $view['execute']);
    }

    /**
     * @test
     */
    function dispatches_onExecute_from_httpMethod()
    {
        $this->d->getRequest()->setRequest(['_method'=>'execute']);
        $view = $this->d->execute();
        $this->assertEquals('executed', $view['execute']);
    }
}