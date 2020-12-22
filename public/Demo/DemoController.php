<?php


use WScore\Pages\AbstractController;

class DemoController extends AbstractController
{
    public function onGet()
    {
        $this->message('please try this demo!');
        return $this->render('form.php', [
            'form' => new DemoForm([], []),
        ]);
    }

    public function onConfirm()
    {
        $inputs = $this->request()->getParsedBody();
        $validation = new DemoValidation();
        $result = $validation->validate($inputs);
        if ($result->fails()) {
            $this->error('invalid inputs!');
            return $this->render('form.php', [
                'form' => new DemoForm($result->getAll(), $result->getMessages()),
            ]);
        }
        $this->setFlash('inputs', $inputs);
        $this->message('please proceed to done!');
        return $this->render('confirm.php', [
            'demo' => new DemoValues($result->getSafe(), []),
            'test' => 'tested',
        ]);
    }

    public function onDone()
    {
        $inputs = $this->getFlash('inputs');
        if (!$inputs) {
            $this->flashError('failed validate input!');
            $this->location('/');
        }
        $validation = new DemoValidation();
        $result = $validation->validate($inputs);
        if ($result->fails()) {
            $this->flashError('failed validate input!');
            $this->location('/');
        }
        $this->message('completed the demo!');
        return $this->render('done.php', [
            'demo' => new DemoValues($result->getSafe(), []),
            'test' => 'tested',
        ]);
    }
}