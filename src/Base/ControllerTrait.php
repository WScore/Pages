<?php
namespace WScore\Pages\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

trait ControllerTrait
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return null|ResponseInterface
     */
    protected function invokeController(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request   = $request;
        $this->response  = $response;
        if (strtoupper($request->getMethod()) === 'HEAD') {
            return $this->onHead();
        }

        return $this->dispatch($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    abstract protected function dispatch(ServerRequestInterface $request);

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * get path info.
     *
     * @return string
     */
    protected function getPathInfo()
    {
        return RequestHelper::getPathInfo($this->request);
    }

    /**
     * get $_POST array.
     *
     * @param null|string $name
     * @return array|null|object
     */
    protected function getPost($name = null)
    {
        $data = $this->request->getParsedBody();
        if (!$name) {
            return $data;
        }
        return array_key_exists($name, $data) ? $data[$name] : null;
    }

    /**
     * get value from flashed data in session.
     *
     * @param string $name
     * @param null   $alt
     * @return mixed
     */
    protected function getFlash($name, $alt = null)
    {
        return RequestHelper::getFlash($this->request, $name, $alt);
    }

    /**
     * get value from session.
     *
     * @param string $name
     * @param null   $alt
     * @return mixed
     */
    protected function getSession($name, $alt = null)
    {
        return RequestHelper::getSession($this->request, $name, $alt);
    }

    /**
     * @param callable $closure
     * @return $this
     */
    protected function with($closure)
    {
        $this->request = Respond::with($this->request, $closure);
        return $this;
    }
    
    /**
     * @return View
     */
    protected function view()
    {
        return Respond::view($this->request, $this->response);
    }

    /**
     * @return Redirect
     */
    protected function redirect()
    {
        return Respond::redirect($this->request, $this->response);
    }

    /**
     * @return Error
     */
    protected function error()
    {
        return Respond::error($this->request, $this->response);
    }

    /**
     * @return null|ResponseInterface
     */
    protected function onHead()
    {
        $this->request = $this->request->withMethod('GET');
        $response      = $this->dispatch($this->request);
        if ($response) {
            $response->getBody()->rewind();
            $response->getBody()->write('');

            return $response;
        }

        return null;
    }

    /**
     * @param string $method
     * @param array  $params
     * @return mixed
     */
    protected function dispatchMethod($method, $params)
    {
        if (!method_exists($this, $method)) {
            return null;
        }
        $refMethod = new \ReflectionMethod($this, $method);
        $refArgs   = $refMethod->getParameters();
        $list      = array();
        foreach ($refArgs as $arg) {
            $key        = $arg->getPosition();
            $name       = $arg->getName();
            $opt        = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val        = isset($params[$name]) ? $params[$name] : $opt;
            $list[$key] = $val;
        }
        $refMethod->setAccessible(true);

        return $refMethod->invokeArgs($this, $list);
    }
}