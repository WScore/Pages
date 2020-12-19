<?php


class DemoController extends \WScore\Pages\AbstractController
{
    public function onGet()
    {
        return $this->render('form.php');
    }

    public function onConfirm()
    {
        return $this->render('confirm.php', [
            'test' => 'tested',
        ]);
    }

    public function onDone()
    {
        return $this->render('done.php', [
            'test' => 'tested',
        ]);
    }
}