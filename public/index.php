<?php

use Laminas\Diactoros\ServerRequestFactory;
use WScore\Pages\Dispatch;

ini_set('display_errors', true);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Demo/DemoController.php';
require_once __DIR__ . '/Demo/DemoValues.php';
require_once __DIR__ . '/Demo/DemoForm.php';
require_once __DIR__ . '/Demo/DemoValidation.php';
require_once __DIR__ . '/Demo/GenderType.php';

$request = ServerRequestFactory::fromGlobals();
$controller = new DemoController();

$view = Dispatch::create($controller, __DIR__ . '/views')
    ->handle($request);

if ($view->isCritical()) {
    $view->setRender('critical.php');
}

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
        label.form-label {
            display: block;
            margin: 1em 0 .5em 0;
            color: #666666;
            font-weight: bold;
        }
        label.choice-items {
            display: inline-block;
            width: 7em;
        }
        span.error {
            color: red;
        }
    </style>
</head>
<body>
<header>
    <a href="/">this is header</a>
</header>
<?php
echo $view->alert();

$view->render();
?>
</body>
</html>