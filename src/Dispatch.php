<?php
namespace WScore\Pages;

/**
 * A simple page-based controller.
 *
 * Class PageController
 * @package Demo\Legacy
 */
class Dispatch
{
    /**
     * @var ControllerAbstract
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageView
     */
    protected $view;

    // +----------------------------------------------------------------------+
    //  construction of dispatch and setting controller object.
    // +----------------------------------------------------------------------+
    /**
     * @param Request $request
     * @param PageView $view
     * @param null|Session $session
     */
    public function __construct( $request, $view, $session=null )
    {
        $this->request = $request;
        $this->view    = $view;
        $this->session = $session ?: Session::getInstance();
    }

    /**
     * @param ControllerAbstract $controller
     */
    public function setController( $controller )
    {
        $controller->inject( 'view',    $this->view );
        $controller->inject( 'request', $this->request );
        $controller->inject( 'session', $this->session );
        $this->controller = $controller;
    }

    /**
     * @param $controller
     * @return $this
     */
    public static function getInstance( $controller )
    {
        /** @var self $me */
        $me = new static(
            new Request(),
            new PageView()
        );
        $me->setController( $controller );
        return $me;
    }

    /**
     * @return PageView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
    // +----------------------------------------------------------------------+
    //  execution of controllers. 
    // +----------------------------------------------------------------------+
    /**
     * @param $execMethod
     * @return array
     */
    protected function execMethod( $execMethod )
    {
        $controller = $this->controller;
        $refMethod  = new \ReflectionMethod( $controller, $execMethod );
        $refArgs    = $refMethod->getParameters();
        $parameters = array();
        foreach( $refArgs as $arg ) {
            $key  = $arg->getPosition();
            $name = $arg->getName();
            $opt  = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val  = $this->request->get( $name, $opt );
            $parameters[$key] = $val;
            $this->view->set( $name, $val );
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs( $controller, $parameters );
    }

    /**
     * @param null|string $method
     * @return PageView
     */
    public function execute( $method=null )
    {
        if( !$method ) $method = $this->request->getMethod();
        $this->view->setCurrentMethod( $method );
        $execMethod = 'on' . ucwords( $method );

        try {

            if( !method_exists( $this->controller, $execMethod ) ) {
                throw new \RuntimeException( 'no method: ' . $method );
            }
            $this->controller->beginController( $method );
            if( $contents = $this->execMethod( $execMethod ) ) {
                $this->view->assign( $contents );
            }
            $this->controller->finishController();

        } catch( \Exception $e ) {
            $this->view->critical( $e->getMessage() );
        }
        return $this->view;
    }
    // +----------------------------------------------------------------------+
}