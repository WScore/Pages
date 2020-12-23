<?php

use WScore\Pages\PageView;
use WScore\Pages\View\Data;

/** @var PageView $this */
/** @var Data $_view */
/** @var DemoValues $form */
$form = $_view->get('demo');

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

<input type="button" value="back to demo's top page" onclick="location.href='/'">