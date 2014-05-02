<?php
namespace WScore\Pages;

abstract class ControllerAbstract
{
    /**
     * @var PageView
     */
    protected $view;

    /**
     * @var PageRequest
     */
    protected $request;

    /**
     * @var PageSession
     */
    protected $session;

    /**
     * @var object[]
     */
    protected $modules = array();

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

    // +----------------------------------------------------------------------+
    //  C.S.R.F. tokens
    // +----------------------------------------------------------------------+
    /**
     * pushes token to session and view objects.
     */
    protected function pushToken()
    {
        $token = $this->session->pushToken();
        $this->view->set( PageSession::TOKEN_ID, $token );
    }

    /**
     * @return bool
     */
    protected function verifyToken()
    {
        $token = $this->request->getCode( PageSession::TOKEN_ID );
        if( !$this->session->verifyToken( $token ) ) {
            return false;
        }
        return true;
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
}