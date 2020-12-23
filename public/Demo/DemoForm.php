<?php


use WScore\Pages\View\Form;
use WScore\Pages\View\Tag;
use WScore\Pages\View\Values;

class DemoForm
{
    /**
     * @var Values
     */
    private $values;

    /**
     * @var Form
     */
    private $forms;

    public function __construct(array $values, array $errors = [])
    {
        $this->values = new Values($values);
        $this->forms = new Form($this->values, $errors);
    }

    /**
     * @return Tag
     */
    public function userName()
    {
        return $this->forms->makeInput('text', 'user_name');
    }

    /**
     * @return Tag
     */
    public function gender()
    {
        return $this->forms->listInputs('radio', GenderType::choices(), 'gender');
    }

    /**
     * @return Tag
     */
    public function comment()
    {
        return $this->forms->makeTextArea('comment')
            ->addAttribute('style', 'width: 30em; height: 8em;');
    }

    /**
     * @param string $key
     * @return string
     */
    public function error($key)
    {
        return $this->forms->error($key);
    }
}