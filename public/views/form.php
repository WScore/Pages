<?php

use WScore\Pages\PageView;

/** @var PageView $this */
$contents = $this->getContents();
?>
<h1>this is form.</h1>

<form action="" method="post">
    <?= $contents->makeCsRfToken(); ?>
    <input type="hidden" name="act" value="confirm">
    <input type="submit" value="Confirm">
</form>
