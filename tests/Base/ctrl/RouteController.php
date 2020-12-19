<?php
namespace tests\Base\ctrl;

use WScore\Pages\Base\DispatchByRouteTrait;
use WScore\Pages\Legacy\AbstractController;

class RouteController extends AbstractController
{
    use DispatchByRouteTrait;

    public function getRoutes()
    {
        return [
            'get:/test' => 'onTest',
            'get:/named/{name}' => 'onNamed',
            'post:/test' => 'onTestPost',
        ];
    }

    public function onTest()
    {
        return $this->view()->asText('tested:test');
    }

    public function onNamed($name)
    {
        return $this->view()->asText('named:'.$name);
    }
}