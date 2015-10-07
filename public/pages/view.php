<?php
use Tuum\Form\DataView;
/** @var DataView $view */

?>
<html>
<head>
    <meta charset="UTF-8"/>
</head>
<body>
<h1>View Page</h1>

<p>view: <?= $view->data->get('view');?></p>

<h2>get method overwritten as view</h2>

<form name="get" method="get" action="" >
    <input type="text" name="name" value="viewed back" />
    <button type="submit">Back to Top</button>
</form>

</body>
</html>