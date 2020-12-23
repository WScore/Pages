<?php

use WScore\Pages\PageView;
use WScore\Pages\View\Data;

/** @var PageView $this */
/** @var Data $_view */
/** @var DemoForm $form */
$form = $_view->get('form');

?>
<h1>this is form.</h1>

<form action="" method="post">
    <?= $_view->makeCsRfToken(); ?>
    <div>
        <label for="user_name" class="form-label">User Name</label>
        <?= $form->userName(); ?>
        <?= $form->error('user_name'); ?>
    </div>
    <div>
        <label for="gender" class="form-label">Gender</label>
        <?= $form->gender(); ?>
        <?= $form->error('gender'); ?>
    </div>

    <div>
        <label for="comment" class="form-label">Comment</label>
        <?= $form->comment(); ?>
    </div>

    <input type="hidden" name="act" value="confirm">
    <input type="submit" value="Confirm">
</form>
