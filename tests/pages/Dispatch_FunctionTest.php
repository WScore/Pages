<?php
namespace tests\pages;

use tests\pages\mocks\TestController;
use WScore\Pages\Dispatch;
use WScore\Pages\Factory;

require_once( dirname(__DIR__).'/autoload.php' );
require_once( __DIR__ . '/mocks/TestController.php' );

class Dispatch_FunctionTest extends \PHPUnit_Framework_TestCase
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
        $this->buildDispatcher();
    }
    
    function buildDispatcher()
    {
        $_REQUEST = array();
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

    // +----------------------------------------------------------------------+
    //  test correct method is executed. 
    // +----------------------------------------------------------------------+
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

    /**
     * @test
     */
    function dispatches_onExecute_by_specifying_method_variable_name()
    {
        $this->d->getRequest()->setMethodName('action');
        $this->d->getRequest()->setRequest(['action'=>'execute']);
        $view = $this->d->execute();
        $this->assertEquals('executed', $view['execute']);
    }

    /**
     * @test
     */
    function executing_non_existence_method_returns_critical_error()
    {
        $view = $this->d->execute('noSuch');
        $this->assertTrue($view->isCritical());
        $this->assertEquals('no method: noSuch', $view->getMessage());
    }

    // +----------------------------------------------------------------------+
    //  test argument is passed to the execute method.
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function onArgument_argument_is_populated()
    {
        $this->d->getRequest()->setRequest(['arg'=>'arg-tested']);
        $view = $this->d->execute('argument');
        $this->assertEquals('arg-tested', $view['argument']);
    }

    /**
     * @test
     */
    function onArgument_without_argument_returns_critical_error()
    {
        $view = $this->d->execute('argument');
        $this->assertTrue($view->isCritical());
        $this->assertEquals('please specify the argument', $view->getMessage());
    }
    // +----------------------------------------------------------------------+
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function savePost_sets_compact_data_to_view_and_loadPost_to_use_the_data()
    {
        $this->d->getRequest()->setRequest(['post1'=>'tested', 'post2'=>'more']);
        $view = $this->d->execute('savePost');
        $this->assertTrue(isset( $view['_savedPost']));
        $this->assertEquals('tested:more', $view['posted']);
        
        // check on what is saved. 
        $saved = $view->get();
        $saved = $this->d->getRequest()->unpack($saved['_savedPost']);
        $this->assertTrue( is_array($saved));
        $this->assertEquals( 'tested', $saved['post1']);
        $this->assertEquals( 'more', $saved['post2']);
        
        // let's call it again. this should create no posted...
        $this->buildDispatcher();
        $view = $this->d->execute('savePost');
        $this->assertTrue(isset( $view['_savedPost']));
        $this->assertEquals(':', $view['posted']);

        // let's call it again with the savedPost. 
        $this->buildDispatcher();
        $this->d->getRequest()->setRequest($saved);
        $view = $this->d->execute('savePost');
        $this->assertTrue(isset( $view['_savedPost']));
        $this->assertEquals('tested:more', $view['posted']);
    }

    /**
     * @test
     */
    function currView_sets_views_automatically()
    {
        $view = $this->d->execute('currView');
        $this->assertEquals('nextView', $view['_method']);
        $this->assertEquals('view is tested', $view['test']);
    }
    // +----------------------------------------------------------------------+
}