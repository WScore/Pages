<?php

use Laminas\Diactoros\ServerRequestFactory;
use WScore\Pages\Dispatch;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/views/DemoController.php';

$request = ServerRequestFactory::fromGlobals();
$controller = new DemoController();

$view = Dispatch::create($controller, __DIR__ . '/views')
    ->handle($request);

?>
<!Document html>
<html lang="en">
<head>
    <title>Page</title>
    <style>
        div.alert {
            margin: 1em;
            padding: 1em;
            border-radius: .5em;
            border: 2px solid gray;
        }
        div.alert-success {
            border: 2px solid green;
        }
        div.alert-danger {
            border: 2px solid red;
        }
    </style>
</head>
<body>
<header>
    this is header
</header>
<?php
if ($view->isCritical()) {
    $view->setRender('critical.php');
}
$view->render();
?>
</body>
</html>