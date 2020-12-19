<?php
namespace tests\pages\mocks;

use WScore\Pages\AbstractController;

class TestController extends AbstractController
{
    /**
     * checking if dispatcher executed this method
     * @var bool
     */
    public $executed = false;

    protected $currentView = [
        'currView' => [
            '_method' => 'nextView',
            'test' => 'view is tested',
        ]
    ];
    
    function onExecute()
    {
        $this->executed = true;
        return ['execute' => 'executed'];
    }
    
    function onArgument( $arg )
    {
        if( !$arg ) {
            $this->critical( 'please specify the argument' );
        }
        return ['argument' => $arg ];
    }
    
    function onSavePost()
    {
        $posts = array();
        $posts['post1'] = $this->request->get( 'post1' );
        $posts['post2'] = $this->request->get( 'post2' );
        $this->savePost($posts);
        $this->set( 'posted', implode(':', $posts) );
    }

    function onCurrView()
    {
        // do nothing.
    }
}