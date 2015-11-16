<?php
namespace WScore\Pages\Legacy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\Referrer;
use Tuum\Respond\Respond;
use WScore\Pages\Base\ControllerTrait;
use Zend\Diactoros\Response;

abstract class AbstractController
{
    use ControllerTrait;

    protected $csrf_name = '_token';

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function invoke(ServerRequestInterface $request)
    {
        $referrer = new Referrer($request);
        $referrer->load();

        // go invoke this controller!
        $response = new Response();
        $response = $this->invokeController($request, $response);
        // done controller.

        if ($response) {
            $referrer->save($response);
        }
        return $response;
    }

    /**
     * @return bool
     */
    protected function csRfGuard()
    {
        $token   = $this->getPost($this->csrf_name);
        $session = Respond::session($this->request);
        return $session->validateToken($token);
    }

    /**
     * @return string
     */
    protected function csRfToken()
    {
        $session = Respond::session($this->request);
        return $session->getToken();
    }
}