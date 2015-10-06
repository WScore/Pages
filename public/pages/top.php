<?php
use Tuum\Form\DataView;
/** @var DataView $view */

?>
<html>
<head>
    <meta charset="UTF-8"/>
</head>
<body>
<h1>Top Page</h1>

<p>name: <?= $view->data->get('name');?></p>

<h2>post method</h2>
<form name="post" method="post" action="" >
    <input type="text" name="name" value="post text" />
    <button type="submit">Post This</button>
</form>

<h2>post with method overwrite as View</h2>
<form name="get" method="get" action="" >
    <input type="hidden" name="_method" value="view"/>
    <input type="text" name="view" value="view text" />
    <button type="submit">View This</button>
</form>

</body>
</html>