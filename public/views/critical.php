<?php

use WScore\Pages\PageView;

/** @var PageView $this */
$contents = $this->getContents();
?>
<h1>Critical Error</h1>
<?= $contents->alert();
