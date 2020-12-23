<?php


use WScore\Pages\View\Values;

class DemoValues extends Values
{
    /**
     * @return string
     */
    public function gender()
    {
        $value = $this->getRaw('gender');
        $choices = GenderType::choices();
        return $choices[$value];
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