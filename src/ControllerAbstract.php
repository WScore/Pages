<?php
namespace WScore\Pages;

abstract class ControllerAbstract
{
    /**
     * @var PageView
     */
    protected $view;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var object[]
     */
    protected $modules = array();

    /**
     * a quick way to setup a view based on method.
     * ex: [ 'method1' => [ 'key1'=>'value1', ... ], ]
     *
     * @var array[]
     */
    protected $methodView = array();

    // +----------------------------------------------------------------------+
    //  generic helpers
    // +----------------------------------------------------------------------+
    /**
     * magic methods to call method in a sub-module.
     *
     * @param string $method
     * @param array $args
     * @throws \RuntimeException
     * @return mixed
     */
    public function __call( $method, $args )
    {
        foreach( $this->modules as $object )
        {
            if( method_exists( $object, $method ) ) {
                return call_user_func_array( [$object,$method], $args );
            }
        }
        throw new \RuntimeException( "cannot find method: {$method} in Sub-Modules." );
    }

    /**
     * Dependency Injection point.
     *
     * @param string $name
     * @param object $object
     */
    public function inject( $name, $object )
    {
        $this->$name = $object;
        $this->modules[ $name ] = $object;
    }

    /**
     * overwrite this to prepare controller before on* method.
     * @param string $method
     */
    public function beginController( $method ) {}

    /**
     * overwrite this to finish controller after on* method.
     */
    public function finishController() {}

    // +----------------------------------------------------------------------+
    //  sort of Response class. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $url
     */
    protected function location( $url )
    {
        $this->view->location( $url );
    }
    
    // +----------------------------------------------------------------------+
    //  C.S.R.F. tokens
    // +----------------------------------------------------------------------+
    /**
     * pushes token to session and view objects.
     */
    protected function pushToken()
    {
        $token = $this->session->pushToken();
        $this->view->set( Session::TOKEN_ID, $token );
    }

    /**
     * @return bool
     */
    protected function verifyToken()
    {
        $token = $this->request->getCode( Session::TOKEN_ID );
        if( !$this->session->verifyToken( $token ) ) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    protected function undoToken()
    {
        $this->session->undoToken();
    }

    // +----------------------------------------------------------------------+
    //  messages and errors. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $message
     */
    protected function message( $message )
    {
        $this->view->message( $message );
    }

    /**
     * @param string $message
     */
    protected function error( $message )
    {
        $this->view->error( $message );
    }

    /**
     * @param string $message
     */
    protected function critical( $message )
    {
        $this->view->critical( $message );
    }

    // +----------------------------------------------------------------------+
    //  flash messages
    // +----------------------------------------------------------------------+
    /**
     * @param $message
     */
    protected function flashMessage( $message )
    {
        $this->session->flash( 'flash-message', $message );
        $this->session->flash( 'flash-error',   false );
    }

    /**
     * @param $message
     */
    protected function flashError( $message )
    {
        $this->session->flash( 'flash-message', $message );
        $this->session->flash( 'flash-error',   true );
    }

    /**
     * set flash message to view object.
     */
    protected function setFlashMessage()
    {
        if( $message = $this->session->get('flash-message') ) {
            if( $this->session->get('flash-error') ) {
                $this->view->error($message);
            } else {
                $this->view->message($message);
            }
        }
    }

    // +----------------------------------------------------------------------+
    //  utilities
    // +----------------------------------------------------------------------+
    /**
     * @param $method
     * @param bool $view
     */
    protected function setMethod( $method, $view=true )
    {
        $this->view->setMethod( $method );
        if( $view &&
            isset( $this->methodView[$method] ) &&
            is_array( $this->methodView[$method] ) ) {
            $this->view->assign( $this->methodView[$method] );
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isSafe( & $value )
    {
        if( !$value ) return false;
        if( preg_match( '/^[-_a-zA-Z0-9]*$/', $value ) ) return true;
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function set( $key, $value )
    {
        $this->view->set( $key, $value );
        return $this;
    }
    // +----------------------------------------------------------------------+
}