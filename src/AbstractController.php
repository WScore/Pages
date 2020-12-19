<?php

namespace WScore\Pages;

use Aura\Session\Segment;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

abstract class AbstractController
{
    const SESSION = 'Session.ID';
    const REQUEST = 'Request.ID';
    const VIEWER = 'Viewer.ID';

    /**
     * @var mixed[]
     */
    protected $modules = array();

    protected $currentView = array();

    // +----------------------------------------------------------------------+
    //  generic helpers
    // +----------------------------------------------------------------------+
    /**
     * magic methods to call method in a sub-module.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($method, $args)
    {
        foreach ($this->modules as $object) {
            if (method_exists($object, $method)) {
                return call_user_func_array([$object, $method], $args);
            }
        }
        throw new RuntimeException("cannot find method: {$method} in Sub-Modules.");
    }

    /**
     * Dependency Injection point.
     *
     * @param string $name
     * @param object $object
     */
    public function inject($name, $object)
    {
        $this->$name = $object;
        $this->modules[$name] = $object;
    }

    /**
     * overwrite this to prepare controller before on* method.
     * @param ServerRequestInterface $request
     */
    public function prepare($request)
    {
        $this->inject(self::REQUEST, $request);
        $this->setFlashMessage();
    }

    /**
     * @return Segment|null
     */
    protected function session()
    {
        return isset($this->modules[self::SESSION]) ? $this->modules[self::SESSION] : null;
    }

    /**
     * @return PageView|null
     */
    protected function pageView()
    {
        return isset($this->modules[self::VIEWER]) ? $this->modules[self::VIEWER] : null;
    }

    /**
     * @return ServerRequestInterface|null
     */
    protected function request()
    {
        return isset($this->modules[self::REQUEST]) ? $this->modules[self::REQUEST] : null;
    }
    // +----------------------------------------------------------------------+
    //  messages and errors. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $message
     */
    protected function message($message)
    {
        $this->pageView()->setMessage($message);
    }

    /**
     * @param string $message
     */
    protected function error($message)
    {
        $this->pageView()->setError($message);
    }

    /**
     * @param string $viewFile
     * @param array $contents
     * @return PageView|null
     */
    protected function render($viewFile, $contents = [])
    {
        $this->pageView()->setRender($viewFile, $contents);

        return $this->pageView();
    }

    // +----------------------------------------------------------------------+
    //  flash messages
    // +----------------------------------------------------------------------+

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function setFlash($key, $value)
    {
        $this->session()->setFlash($key, $value);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getFlash($key)
    {
        return $this->session()->getFlash($key);
    }

    /**
     * @param $message
     */
    protected function flashMessage($message)
    {
        $this->session()->setFlash('flash-message', $message);
        $this->session()->setFlash('flash-error', false);
    }

    /**
     * @param $message
     */
    protected function flashError($message)
    {
        $this->session()->setFlash('flash-message', $message);
        $this->session()->setFlash('flash-error', true);
    }

    /**
     * set flash message to view object.
     */
    protected function setFlashMessage()
    {
        if ($message = $this->session()->get('flash-message')) {
            if ($this->session()->get('flash-error')) {
                $this->pageView()->setError($message);
            } else {
                $this->pageView()->setMessage($message);
            }
        }
    }
}