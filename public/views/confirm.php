<?php

use WScore\Pages\PageView;

/** @var PageView $this */
$contents = $this->getContents();
/** @var DemoValues $form */
$form = $contents->get('demo');
?>
<h1>this is confirm</h1>

<form action="" method="post">
    <?= $contents->makeCsRfToken(); ?>

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

    <input type="hidden" name="act" value="done">
    <input type="submit" value="Done!">

</form>
