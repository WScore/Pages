<?php
use Tuum\Form\DataView;
/** @var DataView $view */

?>
<html>
<head>
    <meta charset="UTF-8"/>
</head>
<body>
<h1>Post Page</h1>

<p>name: <?= $view->data->get('name');?></p>

<h2>post method</h2>
<form name="get" method="get" action="" >
    <input type="text" name="name" value="posted back" />
    <button type="submit">Back to Top</button>
</form>

</body>
</html>