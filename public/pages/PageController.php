<?php

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Responder\ViewData;
use WScore\Pages\Base\DispatchByMethodTrait;
use Tuum\Respond\Responder;
use WScore\Pages\Legacy\AbstractController;

class PageController extends AbstractController
{
    use DispatchByMethodTrait;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return PageController
     */
    public static function forge()
    {
        return new self();
    }

    /**
     * @param null|string $name
     * @return ResponseInterface
     */
    public function onGet($name = null)
    {
        return $this->view()
            ->withData('name', $name)
            ->asView('top');
    }

    /**
     * @param null|string $view
     * @return ResponseInterface
     */
    public function onView($view = null)
    {
        return $this
            ->with(function (ViewData $data) use ($view) {
                $data->setData('view', $view);

                return $data;
            })
            ->view()->asView('view');
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        $name = $this->getPost('name');
        return $this->view()
            ->withData('name', $name)
            ->asView('post');
    }
}