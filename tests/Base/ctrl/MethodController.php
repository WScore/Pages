<?php
namespace tests\Base\ctrl;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Service\SessionStorageInterface;
use WScore\Pages\Base\DispatchByMethodTrait;
use WScore\Pages\Legacy\AbstractController;

class MethodController extends AbstractController
{
    use DispatchByMethodTrait;

    public function onGet()
    {
        return $this->view()
            ->asText('tested onGet');
    }

    public function onRedirect()
    {
        return $this->redirect()
            ->toPath('tested/redirect');
    }

    public function onForbidden()
    {
        return $this->error()
            ->forbidden();
    }

    public function onTest()
    {
        $messages = [];
        if ($this->getRequest() instanceof ServerRequestInterface) {
            $messages['getRequest'] = 'tested-request';
        }
        if ($this->session() instanceof SessionStorageInterface) {
            $messages['session'] = 'tested-session';
        }
        $messages['getPost'] = $this->getPost('posted');
        $messages['getPathInfo'] = $this->getPathInfo();
        return $this->view()->asJson($messages);
    }

    public function onNamed($name = 'bad')
    {
        return $this->view()
            ->asText('named:'.$name);
    }
}
