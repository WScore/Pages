<?php
namespace WScore\Pages\Legacy;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewStream;

/**
 * build $session and $responder, first.
 */
class RequestBuilder
{
    /**
     * @var ServerRequestInterface
     */
    public $request;

    /**
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * the all in one forge method.
     *
     * @param string $view_dir
     * @return ServerRequestInterface
     */
    public static function forge($view_dir)
    {
        return RequestBuilder::forgeFromGlobal($GLOBALS)
            ->withTuum(RequestBuilder::buildResponder($view_dir))
            ->getRequest();
    }

    /**
     * @param array $global
     * @return RequestBuilder
     */
    public static function forgeFromGlobal($global = [])
    {
        $request = RequestHelper::createFromGlobal($global ?: $GLOBALS);
        $self    = new self($request);
        return $self;
    }

    /**
     * @param string $path
     * @param string $method
     * @return RequestBuilder
     */
    public static function forgeFromPath($path, $method = 'GET')
    {
        $request = RequestHelper::createFromPath($path, $method);
        $self    = new self($request);
        return $self;
    }

    /**
     * build Responder.
     *
     * @param string $view_dir
     * @param array  $options
     * @return Responder
     */
    public static function buildResponder($view_dir, $options = [])
    {
        $options += ['error_option' => [], 'contents_view' => ''];
        $viewStream = ViewStream::forge($view_dir);
        $responder  = Responder::build(
            $viewStream,
            ErrorView::forge($viewStream, $options['error_option']),
            $options['contents_view']
        );
        return $responder;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Responder $responder
     * @return $this
     */
    public function withResponder($responder)
    {
        $this->request = RequestHelper::withResponder($this->request, $responder);
        return $this;
    }

    /**
     * set SessionStorageInterface.
     *
     * @param string $app_name
     * @return $this
     */
    public function withSession($app_name = 'page-app')
    {
        $this->request = RequestHelper::withSessionMgr(
            $this->request,
            SessionStorage::forge($app_name)
        );
        return $this;
    }

    /**
     * overwrite http method with POSTed value (with $key).
     *
     * @param string $key
     * @return $this
     */
    public function withMethod($key = '_method')
    {
        $this->request = RequestHelper::withMethod($this->request, $key);
        return $this;
    }

    /**
     * @param Responder $responder
     * @return $this
     */
    public function withTuum($responder)
    {
        return $this->withResponder($responder)->withSession()->withMethod();
    }
}
