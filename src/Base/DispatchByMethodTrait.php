<?php
namespace WScore\Pages\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder\View;

trait DispatchByMethodTrait
{
    /**
     * @param string $method
     * @param array  $params
     * @return null|ResponseInterface
     */
    abstract protected function dispatchMethod($method, $params);

    /**
     * @return View
     */
    abstract protected function view();

    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    protected function dispatch(ServerRequestInterface $request)
    {
        /*
         * set up request information
         */
        $params = $request->getQueryParams();
        $method = RequestHelper::getMethod($request);
        if (strtoupper($method) === 'OPTIONS') {
            return $this->onOptions();
        }
        /*
         * invoke based on the method name i.e. onMethod(...)
         * also setup arguments from route parameters and get query.
         */
        $method = 'on' . ucwords($method);
        return $this->dispatchMethod($method, $params);
    }

    /**
     * @return ResponseInterface
     */
    private function onOptions()
    {
        $refClass = new \ReflectionObject($this);
        $methods  = $refClass->getMethods();
        $options  = [];
        foreach ($methods as $method) {
            if (preg_match('/^on([_a-zA-Z0-9]+)$/', $method->getName(), $match)) {
                $options[] = strtoupper($match[1]);
            }
        }
        $options = array_unique($options);
        sort($options);
        $list = implode(',', $options);
        $this->view()->asResponse('', 200, ['Allow' => $list]);
    }

}