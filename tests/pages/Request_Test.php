<?php
namespace tests\pages;

use WScore\Pages\Request;

require_once( dirname(__DIR__).'/autoload.php' );

class Request_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    var $req;

    static function setupBeforeClass()
    {
        class_exists( 'WScore\Pages\Request' );
    }

    function setup()
    {
        $this->req = new Request();
        $_SERVER['REQUEST_METHOD'] = 'get';
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pages\Request', get_class( $this->req ) );
        $this->assertTrue( isset( $_REQUEST ) );
        $this->assertTrue( isset( $_SERVER ) );
    }

    /**
     * @test
     */
    function getMethod_gets_http_get_request_method()
    {
        $this->assertEquals( 'get', $this->req->getMethod() );
    }

    /**
     * @test
     */
    function getMethod_gets_http_post_request_method()
    {
        $_SERVER['REQUEST_METHOD'] = 'post';
        $this->assertEquals( 'post', $this->req->getMethod() );
    }

    /**
     * @test
     */
    function getMethod_returns_value_set_in_method()
    {
        $_REQUEST['_method'] = 'test_method';
        $this->assertEquals( 'test_method', $this->req->getMethod() );
    }

    /**
     * @test
     */
    function getMethod_returns_value_as_defined_by_setMethod()
    {
        $_REQUEST['action'] = 'test_action';
        $this->req->setMethodName( 'action' );
        $this->assertEquals( 'test_action', $this->req->getMethod() );
    }

    /**
     * @test
     */
    function getData_gets_value_in_request()
    {
        $_REQUEST[ 'test' ] = 'test_get';
        $this->assertEquals( 'test_get', $this->req->get('test') );
        $this->assertEquals( 'test_get', $this->req->getCode('test') );
        $this->assertEquals( 'test_get', $this->req->getString('test') );
        $_REQUEST[ 'risky' ] = "test\0get";
        $this->assertEquals( 'test get', $this->req->get('risky') );
        $this->assertEquals( null, $this->req->getCode('risky') );
        $this->assertEquals( 'test get', $this->req->getString('risky') );
    }
}
