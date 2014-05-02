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
     * @var PageRequest
     */
    protected $request;

    /**
     * @var PageView
     */
    protected $view;

    // +----------------------------------------------------------------------+
    //  construction of dispatch and setting controller object.
    // +----------------------------------------------------------------------+
    /**
     * @param PageRequest $request
     * @param PageView    $view
     */
    public function __construct( $request, $view )
    {
        $this->request = $request;
        $this->view    = $view;
    }

    /**
     * @param ControllerAbstract $controller
     */
    public function setController( $controller )
    {
        $controller->inject( 'view',    $this->view );
        $controller->inject( 'request', $this->request );
        $controller->inject( 'session', PageSession::getInstance() );
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
            new PageRequest(),
            new PageView()
        );
        $me->setController( $controller );
        return $me;
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
        }
        $refMethod->setAccessible(true);
        $refMethod->invokeArgs( $controller, $parameters );
    }

    /**
     * @param null|string $method
     * @return PageView
     */
    public function execute( $method='_method' )
    {
        $method = $this->request->getMethod( $method );
        $execMethod = 'on' . ucwords( $method );

        try {

            if( !method_exists( $this->controller, $execMethod ) ) {
                throw new \RuntimeException( 'no method: ' . $method );
            }
            $this->execMethod( $execMethod );

        } catch( \Exception $e ) {
            $this->view->critical( $e->getMessage() );
        }
        return $this->view;
    }
    // +----------------------------------------------------------------------+
}