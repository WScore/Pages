<?php
namespace WScore\Pages\Legacy;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;

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
     * @param array $global
     * @return RequestBuilder
     */
    public static function forgeFromGlobal($global = [])
    {
        $request = ReqBuilder::createFromGlobal($global ?: $GLOBALS);
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
        $request = ReqBuilder::createFromPath($path, $method);
        $self    = new self($request);
        return $self;
    }

    /**
     * build Responder.
     *
     * @param string $view_dir
     * @param array  $options
     * @param string $name
     * @return $this
     */
    public function withResponder($view_dir, $options = [], $name = 'tuum-app')
    {
        $errors    = isset($options['error_option']) ? $options['error_option']: [];
        $contents  = isset($options['contents_view']) ? $options['contents_view']: null;
        $viewer    = TuumViewer::forge($view_dir);
        $responder = ResponderBuilder::withView($viewer, $errors, $contents);
        if (!is_null($name)) {
            $responder = $responder->withSession(SessionStorage::forge($name));
        }
        $this->request = Respond::withResponder($this->request, $responder);
        return $this;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * overwrite http method with POSTed value (with $key).
     *
     * @param string $key
     * @return $this
     */
    public function withMethod($key = '_method')
    {
        $this->request = ReqAttr::withMethod($this->request, $key);
        return $this;
    }
}
