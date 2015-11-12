<?php
namespace tests\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
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
}
