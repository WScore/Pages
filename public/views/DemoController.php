<?php


use WScore\Pages\AbstractController;

class DemoController extends AbstractController
{
    public function onGet()
    {
        return $this->render('form.php', [
            'form' => new DemoForm([], []),
        ]);
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