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

    /**
     * @test
     */
    function getHidden_returns_hidden_tag_or_value_or_tag_with_name()
    {
        $value = 'testHidden';
        $key   = 'test';
        $this->view->set( $key, $value );
        $this->assertEquals( $value, $this->view->getHidden( $key, false ) );
        $this->assertContains( "value=\"$value", $this->view->getHidden( $key ) );
        $this->assertContains( "name=\"$key",    $this->view->getHidden( $key ) );
        $this->assertContains( "value=\"$value", $this->view->getHidden( $key, 'hide' ) );
        $this->assertContains( "name=\"hide",    $this->view->getHidden( $key, 'hide' ) );
    }

    /**
     * @test
     */
    function assign_sets_array_as_data()
    {
        $data = [ 'test' => 'tested', 'more' => 'done' ];
        $this->view->assign($data);
        $this->assertEquals( 'tested', $this->view->get('test') );
        $this->assertEquals( 'done', $this->view->get('more') );
    }

    /**
     * @test
     */
    function collection_always_return_an_array()
    {
        $data = [ 'single' => 'tested', 'array' => [ 'tested', 'more' ] ];
        $this->view->assign($data);
        $this->assertEquals( array(), $this->view->collection('none') );
        $this->assertEquals( ['tested'], $this->view->collection('single') );
        $this->assertEquals( [ 'tested', 'more' ], $this->view->collection('array') );
    }

    /**
     * @test
     */
    function is_checks_value_against_key()
    {
        $data = [ 'single' => '1', 'array' => [ '2', '3' ] ];
        $this->view->assign($data);
        $this->assertTrue(  $this->view->is( 'single', 1 ) );
        $this->assertFalse( $this->view->is( 'single', 2 ) );
        $this->assertFalse( $this->view->is( 'array',  1 ) );
        $this->assertTrue(  $this->view->is( 'array',  2 ) );
        $this->assertTrue(  $this->view->is( 'array',  3 ) );
    }

    /**
     * @test
     */
    function h_applies_htmlSpecialChars()
    {
        $this->assertEquals( 'tested', $this->view->h( 'tested' ) );
        $this->assertEquals( '&lt;b&gt;b&lt;/b&gt;', $this->view->h( '<b>b</b>' ) );
    }

    /**
     * @test
     */
    function getOffset_returns_htmlSafe_characters()
    {
        $data = [ 'text' => 'tested', 'html' => '<b>b</b>' ];
        $this->view->assign($data);
        $this->view['text'] = 'tested';
        $this->view['html'] = '<b>b</b>';
        $this->assertEquals( 'tested',  $this->view['text'] );
        $this->assertEquals( '&lt;b&gt;b&lt;/b&gt;',  $this->view['html'] );
    }

    /**
     * @test
     */
    function toPass_is_preset()
    {
        $data = [
            '_method' => 'testing',
            '_token' => 'tested',
            '_savedPost' => 'saved data',
            'test' => 'normal',
        ];
        $this->view->assign( $data );
        $passed = $this->view->getPass();
        $this->assertContains( '<input type="hidden" ', $passed );
        $this->assertContains( 'name="_method"', $passed );
        $this->assertContains( 'name="_token"', $passed );
        $this->assertContains( 'name="_savedPost"', $passed );
        $this->assertNotContains( 'name="test"', $passed );
    }
}
