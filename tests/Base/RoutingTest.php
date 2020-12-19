<?php
namespace tests\Base;

use tests\Base\ctrl\RouteController;
use WScore\Pages\Legacy\RequestBuilder;

require_once(dirname(__DIR__).'/autoload.php');

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $_SESSION = [];
    }

    function tearDown()
    {
        if (isset($_SESSION)) {
            unset($_SESSION);
        }
    }

    function test_route_to_test()
    {
        $controller = new RouteController();
        $request    = RequestBuilder::forgeFromPath('test')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals('tested:test', $response->getBody()->__toString());
    }

    function test_named_route_to_set_parameter()
    {
        $controller = new RouteController();
        $request    = RequestBuilder::forgeFromPath('named/tested')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals('named:tested', $response->getBody()->__toString());
    }

    function test_options_method_returns_allowed_methods()
    {
        $controller = new RouteController();
        $request    = RequestBuilder::forgeFromPath('test', 'OPTIONS')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals('GET,HEAD,OPTIONS,POST', $response->getHeaderLine('Allow'));

        $request    = RequestBuilder::forgeFromPath('named/option', 'OPTIONS')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals('GET,HEAD,OPTIONS', $response->getHeaderLine('Allow'));
    }
}