<?php
namespace WScore\Pages;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;
use WScore\Pages\Base\ControllerTrait;

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
        $request  = RequestHelper::loadReferrer($request);
        $response = ResponseHelper::createResponse('');
        $response = $this->invokeController($request, $response);
        if ($response && $response->getStatusCode() == 200) {
            RequestHelper::saveReferrer($request);
        }
        return $response;
    }

    /**
     * @return bool
     */
    protected function csRfGuard()
    {
        $token   = $this->getPost($this->csrf_name);
        $session = RequestHelper::getSessionMgr($this->request);
        return $session->validateToken($token);
    }

    /**
     * @return string
     */
    protected function csRfToken()
    {
        $session = RequestHelper::getSessionMgr($this->request);
        return $session->getToken();
    }
}