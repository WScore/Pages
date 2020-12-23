<?php

use WScore\Pages\PageView;
use WScore\Pages\View\Data;

/** @var PageView $this */
/** @var Data $_view */
/** @var DemoValues $form */
$form = $_view->get('demo');
?>
<h1>this is confirm</h1>

<form action="" method="post">
    <?= $_view->makeCsRfToken(); ?>

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
