<?php

use Psr\Http\Message\ResponseInterface;
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
            ->with('name', $name ?: '** not set **')
            ->asView('top');
    }

    /**
     * @param null|string $view
     * @return ResponseInterface
     */
    public function onView($view = null)
    {
        return $this->view()
            ->with('view', $view)
            ->asView('view');
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        $name = $this->getPost('name');
        return $this->view()
            ->with('name', $name)
            ->asView('post');
    }
}