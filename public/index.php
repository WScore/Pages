<?php
/**
 * a sample for page-controller using Tuum/Respond.
 *
 * This is for legacy site.
 */
use Tuum\Respond\Responder;
use WScore\Pages\Legacy\RequestBuilder;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * load composer's autoloader.
 */
if (file_exists($file = dirname(__DIR__) . "/vendor/autoload.php")) {
    /** @noinspection PhpIncludeInspection */
    require_once $file;
} else {
    die('cannot find composer autoloader.');
}
include __DIR__ . "/pages/PageController.php";

/**
 * build request
 */
$request = RequestBuilder::forgeFromGlobal($GLOBALS)
    ->withResponder(__DIR__ . '/pages')
    ->withMethod()
    ->getRequest();

/**
 * invoke controller.
 */
$controller = PageController::forge();
$response   = $controller->invoke($request);
if ($response) {
    $emitter = new SapiEmitter();
    $emitter->emit($response);
}