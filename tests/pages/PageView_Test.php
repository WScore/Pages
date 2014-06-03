<?php
namespace tests\pages;

use WScore\Pages\PageView;

require_once( dirname(__DIR__).'/autoload.php' );

class PageView_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageView
     */
    var $view;

    public function setup()
    {
        $this->view = new PageView();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pages\PageView', get_class($this->view));
    }

    /**
     * @test
     */
    function set_value_to_view_then_get_the_value()
    {
        $value = 'set and get value';
        $this->view->set( 'test', $value );
        $this->assertEquals( $value, $this->view->get('test') );
        $this->assertTrue( $this->view->exists('test') );
    }

    /**
     * @test
     */
    function pass_key_value_will_appear_in_getPass()
    {
        $value = 'test-pass';
        $key   = '_testPass';
        $this->view->set( "_set_only", 'test' );
        $this->view->pass( $key, $value );
        $tags  = $this->view->getPass();
        $this->assertContains( "value=\"$value", $tags );
        $this->assertContains( "name=\"$key", $tags );
        $this->assertNotContains( '_set_only', $tags );
    }
}
