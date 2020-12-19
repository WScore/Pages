<?php

namespace WScore\Pages;

use Aura\Session\Session;
use Aura\Session\SessionFactory;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/**
 * A simple page-based controller.
 *
 * Class PageController
 * @package Demo\Legacy
 */
class Dispatch
{
    /**
     * @var AbstractController
     */
    protected $controller;

    /**
     * @var ServerRequestInterface
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

    /**
     * @var string
     */
    private $action = 'act';

    // +----------------------------------------------------------------------+
    //  construction of dispatch and setting controller object.
    // +----------------------------------------------------------------------+
    /**
     * @param AbstractController $controller
     * @param PageView $view
     * @param Session $session
     */
    public function __construct($controller, $view, $session)
    {
        $this->view = $view;
        $this->session = $session;
        $this->controller = $controller;
        $this->setController($this->controller);
    }

    /**
     * @param AbstractController $controller
     * @param string $viewRoot
     * @return Dispatch
     */
    public static function create($controller, $viewRoot)
    {
        $factory = new SessionFactory();
        $session = $factory->newInstance($_COOKIE);
        $view = new PageView($session, $viewRoot);

        return new Dispatch($controller, $view, $session);
    }

    /**
     * @param AbstractController $controller
     */
    protected function setController($controller)
    {
        $controller->inject('view', $this->view);
        $controller->inject('session', $this->session->getSegment('app'));
        $this->controller = $controller;
    }

    public function handle($request)
    {
        $this->request = $request;
        $method = $this->request->getMethod();
        if (isset($this->request->getParsedBody()[$this->action])) {
            $method = $this->request->getParsedBody()[$this->action];
        } elseif (isset($this->request->getQueryParams()[$this->action])) {
            $method = $this->request->getQueryParams()[$this->action];
        }
        $execMethod = 'on' . ucwords($method);
        return $this->execute($execMethod);
    }

    /**
     * @overwrite this method if necessary.
     */
    protected function security()
    {
        if ($this->request->getMethod() === 'POST') {
            $input = $this->request->getParsedBody();
            $token = isset($input[PageView::CSRF_TOKEN]) ? $input[PageView::CSRF_TOKEN] : null;
            if (!$token) {
                throw new RuntimeException('Please set CSRF token.');
            }
            if (!$this->session->getCsrfToken()->isValid($token)) {
                throw new RuntimeException('Invalid CSRF token.');
            }
        }
    }

    // +----------------------------------------------------------------------+
    //  execution of controllers. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $execMethod
     * @param array $inputs
     * @return array
     * @throws ReflectionException
     */
    protected function execMethod($execMethod, $inputs = [])
    {
        $controller = $this->controller;
        $refMethod = new ReflectionMethod($controller, $execMethod);
        $refArgs = $refMethod->getParameters();
        $parameters = array();
        foreach ($refArgs as $arg) {
            $key = $arg->getPosition();
            $name = $arg->getName();
            $opt = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val = isset($inputs[$name]) ? $inputs[$name] : $opt;
            $parameters[$key] = $val;
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs($controller, $parameters);
    }

    /**
     * @param string $execMethod
     * @return PageView
     */
    public function execute($execMethod)
    {
        if (!method_exists($this->controller, $execMethod)) {
            $this->view->setCritical('no such method: ' . $execMethod);
            return $this->view;
        }
        $inputs = array_merge($this->request->getServerParams(), $this->request->getQueryParams());
        try {

            $this->security();
            $this->controller->prepare($this->request);
            $response = $this->execMethod($execMethod, $inputs);

            if ($response instanceof PageView) {
                $this->view = $response;
                return $response;
            }
            if (is_string($response)) {
                $this->view->setRender($response);
                return $this->view;
            }

        } catch (Exception $e) {
            $this->view->setCritical($e->getMessage());
        }
        return $this->view;
    }
    // +----------------------------------------------------------------------+
}