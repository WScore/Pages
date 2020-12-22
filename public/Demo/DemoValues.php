<?php


use WScore\Pages\View\Values;

class DemoValues extends Values
{
    public function __construct(array $values, array $errors)
    {
        parent::__construct($values, $errors);
    }

    /**
     * @return string
     */
    public function gender()
    {
        $value = $this->getRaw('gender');
        $choices = GenderType::choices();
        if (!isset($choices[$value])) {
            return $this->makeErrorMessage('invalid value: ' . $value);
        }
        return $choices[$value] . $this->error('gender');
    }

    /**
     * @return string
     */
    public function comment()
    {
        $value = $this->html('comment');
        return nl2br($value);
    }
}