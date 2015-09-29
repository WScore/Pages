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

    protected $csrf_name = '_method';

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
        $token   = array_key_exists($this->csrf_name, $this->request->getParsedBody()) ?
            $this->request->getParsedBody()[$this->csrf_name]:null;
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