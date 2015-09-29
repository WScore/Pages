<?php
namespace WScore\Pages;

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
     * @var string
     */
    public $app_name = 'page-app';

    /**
     * @var string    view directory
     */
    public $view_dir = '';

    /**
     * @var array     error options
     */
    public $error_option = [];

    /**
     * @var string|null   view file name for contents view (for ViewStreamInterface::getContent)
     */
    public $contents_view = null;

    /**
     * @param $view_dir
     * @return RequestBuilder
     */
    public static function forge($view_dir)
    {
        $self           = new self();
        $self->view_dir = $view_dir;
        return $self;
    }

    /**
     * set contents-view information.
     *
     * @param null   $contents_view
     * @return $this
     */
    public function contents($contents_view = null)
    {
        $this->contents_view = $contents_view;
        return $this;
    }

    /**
     * set error view information.
     *
     * @param array $error_option
     * @return $this
     */
    public function error($error_option)
    {
        $this->error_option = $error_option;
        return $this;
    }

    /**
     * @param array $global
     * @return ServerRequestInterface
     */
    public function build($global = [])
    {
        $session    = SessionStorage::forge($this->app_name);
        $viewStream = ViewStream::forge($this->view_dir);
        $responder  = Responder::build(
            $viewStream,
            ErrorView::forge($viewStream, $this->error_option),
            $this->contents_view
        );

        /**
         * create a $request filled with various attributes.
         */
        $request = RequestHelper::createFromGlobal($global ?: $GLOBALS);
        $request = RequestHelper::withResponder($request, $responder);
        $request = RequestHelper::withSessionMgr($request, $session);
        $request = RequestHelper::withMethod($request);

        return $request;
    }
}
