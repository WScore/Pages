<?php
namespace tests\Base\ctrl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WScore\Pages\Legacy\AbstractController;

class TestController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    protected function dispatch(ServerRequestInterface $request)
    {
        return $this->view()
            ->withMessage('dispatched')
            ->withAlertMsg('noticed')
            ->withErrorMsg('withoutError')
            ->withInputData(['input'=>'tested'])
            ->withInputErrors(['has'=>'error'])
            ->asView('responded');
    }
}