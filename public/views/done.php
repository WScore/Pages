<?php

use WScore\Pages\PageView;

/** @var PageView $this */
$contents = $this->getContents();
$form = $contents->get('demo');

?>
<h1>this is done</h1>

<div>
    <label for="user_name" class="form-label">User Name</label>
    <?= $form->html('user_name'); ?>
</div>
<div>
    <label for="gender" class="form-label">Gender</label>
    <?= $form->gender(); ?>
</div>

<div>
    <label for="comment" class="form-label">Comment</label>
    <?= $form->comment('comment'); ?>
</div>