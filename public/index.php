<?php
/**
 * a sample for page-controller using Tuum/Respond.
 *
 * This is for legacy site.
 */
use Tuum\Respond\Responder;
use Tuum\Respond\ResponseHelper;
use WScore\Pages\Legacy\RequestBuilder;

/**
 * load composer's autoloader.
 */
if (file_exists($file = dirname(__DIR__) . "/vendor/autoload.php")) {
    /** @noinspection PhpIncludeInspection */
    require_once $file;
} elseif (file_exists($file = dirname(dirname(dirname(dirname(__DIR__)))) . "/vendor/autoload.php")) {
    /** @noinspection PhpIncludeInspection */
    require_once $file;
} else {
    die('cannot find composer autoloader.');
}
include __DIR__ . "/pages/PageController.php";

/**
 * build request
 */
$request = RequestBuilder::forge(__DIR__ . '/pages');

/**
 * invoke controller.
 */
$controller = PageController::forge();
$response   = $controller->invoke($request);
if ($response) {
    ResponseHelper::emit($response);
}