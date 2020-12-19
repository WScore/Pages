<?php

use Laminas\Diactoros\ServerRequestFactory;
use tests\pages\mocks\TestController;
use WScore\Pages\Dispatch;

require_once __DIR__ . '/../vendor/autoload.php';

$request = ServerRequestFactory::fromGlobals();
$controller = new TestController();

$view = Dispatch::create($controller, __DIR__ . '/views')
    ->dispatch($request);

if ($view->isCritical()) {
    include __DIR__ . '/views/critical.php';
} else {
    $view->render();
}