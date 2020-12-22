<?php

use WScore\Pages\PageView;

/** @var PageView $this */
$contents = $this->getContents();
/** @var DemoForm $form */
$form = $contents->get('form');
?>
<h1>this is form.</h1>

<form action="" method="post">
    <?= $contents->makeCsRfToken(); ?>
    <div>
        <label for="user_name" class="form-label">User Name</label>
        <?= $form->userName(); ?>
    </div>
    <div>
        <label for="gender" class="form-label">Gender</label>
        <?= $form->gender(); ?>
    </div>

    <div>
        <label for="comment" class="form-label">Comment</label>
        <?= $form->comment(); ?>
    </div>

    <input type="hidden" name="act" value="confirm">
    <input type="submit" value="Confirm">
</form>
