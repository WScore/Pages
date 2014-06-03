<?php
namespace tests\pages;

use WScore\Pages\Request;

require_once( dirname(__DIR__).'/autoload.php' );

class RequestUA_Test extends \PHPUnit_Framework_TestCase
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

    /**
     * @test
     */
    function isTablet_returns_true_for_iPad_UA_()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10';
        $this->assertTrue( $this->req->isTablet() );
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X; en-us) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B176 Safari/7534.48.3';
        $this->assertTrue( $this->req->isTablet() );
    }

    /**
     * @test
     */
    function isMobile_returns_false_for_iPad_UA_()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10';
        $this->assertFalse( $this->req->isMobile() );
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X; en-us) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B176 Safari/7534.48.3';
        $this->assertFalse( $this->req->isMobile() );
    }

    /**
     * @test
     */
    function isTable_returns_false_for_iPhone_UA()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3';
        $this->assertFalse( $this->req->isTablet() );
    }

    /**
     * @test
     */
    function isMobile_returns_true_for_iPhone_UA()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3';
        $this->assertTrue( $this->req->isMobile() );
    }

    /**
     * @test
     */
    function isTable_returns_false_for_android_phone_UA()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; U; Android 2.2; en-gb; Nexus One Build/FRF50) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';
        $this->assertFalse( $this->req->isTablet() );
    }

    /**
     * @test
     */
    function isMobile_returns_true_for_android_phone_UA()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; U; Android 2.2; en-gb; Nexus One Build/FRF50) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';
        $this->assertTrue( $this->req->isMobile() );
    }

    /**
     * @test
     */
    function isTable_returns_false_for_chrome_UA_on_phone()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; <Android Version>; <Build Tag etc.>) AppleWebKit/<WebKit Rev> (KHTML, like Gecko) Chrome/<Chrome Rev> Mobile Safari/<WebKit Rev>';
        $this->assertTrue( $this->req->isMobile() );
        $this->assertFalse( $this->req->isTablet() );
    }

    /**
     * @test
     */
    function isMobile_returns_true_for_chrome_UA_on_tablet()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; <Android Version>; <Build Tag etc.>) AppleWebKit/<WebKit Rev>(KHTML, like Gecko) Chrome/<Chrome Rev> Safari/<WebKit Rev>';
        $this->assertFalse( $this->req->isMobile() );
        $this->assertTrue( $this->req->isTablet() );
    }
}
