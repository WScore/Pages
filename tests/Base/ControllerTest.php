<?php
namespace tests\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use tests\Base\ctrl\MethodController;
use tests\Base\ctrl\TestController;
use WScore\Pages\Legacy\RequestBuilder;

require_once(dirname(__DIR__).'/autoload.php');

class ControllerTest extends \PHPUnit_Framework_TestCase
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
    
    /**
     * @test
     */
    function test_simple_controller_returns_all_view_properties()
    {
        $controller = new TestController();
        $request    = RequestBuilder::forgeFromPath('test')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $body       = $response->getBody();
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertTrue($body instanceof StreamInterface);
        $this->assertEquals('Zend\Diactoros\Response', get_class($response));
        $this->assertEquals('Zend\Diactoros\Stream', get_class($body));
    }

    function test_head_method_returns_empty_response()
    {
        $controller = new TestController();
        $request    = RequestBuilder::forgeFromPath('test', 'HEAD')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $body       = $response->getBody();
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertTrue($body instanceof StreamInterface);
        $this->assertEquals('Zend\Diactoros\Response', get_class($response));
        $this->assertEquals('Zend\Diactoros\Stream', get_class($body));
        $this->assertEquals('', $body->__toString());
    }

    function test_dispatch_by_method_returns_onGet()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'GET')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals('tested onGet', $response->getBody()->__toString());
    }

    function test_redirect_returns_redirection_response()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'REDIRECT')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tested/redirect', $response->getHeaderLine('Location'));
    }

    function test_forbidden_error_returns_error_response()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'FORBIDDEN')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertEquals(403, $response->getStatusCode());
    }

    function test_various_controller_helper_methods()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'TEST')
            ->withResponder(__DIR__)
            ->getRequest()
            ->withParsedBody(['posted' => 'tested-post']);
        $response   = $controller->invoke($request);
        $json = json_decode($response->getBody()->__toString());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('tested-request', $json->getRequest);
        $this->assertEquals('tested-post', $json->getPost);
        $this->assertEquals('tested-session', $json->session);
        $this->assertEquals('test', $json->getPathInfo);
    }

    function test_options_method_returns_all_methods()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'OPTIONS')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $allowed = explode(',', $response->getHeaderLine('Allow'));
        $this->assertTRUE(in_array('FORBIDDEN', $allowed));
        $this->assertTRUE(in_array('GET', $allowed));
        $this->assertTRUE(in_array('HEAD', $allowed));
        $this->assertTRUE(in_array('OPTIONS', $allowed));
        $this->assertTRUE(in_array('TEST', $allowed));
    }

    function test_named_parameter()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'NAMED')
            ->withResponder(__DIR__)
            ->getRequest()
            ->withQueryParams(['name' => 'testing']);
        $response   = $controller->invoke($request);
        $this->assertEquals('named:testing', $response->getBody()->__toString());
    }

    function test_bad_method_returns_null()
    {
        $controller = new MethodController();
        $request    = RequestBuilder::forgeFromPath('test', 'BAD_METHOD')
            ->withResponder(__DIR__)
            ->getRequest();
        $response   = $controller->invoke($request);
        $this->assertNull($response);
    }
}
