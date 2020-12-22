<?php


use WScore\Validation\Dio;
use WScore\Validation\ValidationFactory;

class DemoValidation
{
    /**
     * @var ValidationFactory
     */
    private $v;

    public function __construct()
    {
        $this->v = new ValidationFactory('en');
    }

    /**
     * @param array $input
     * @return Dio
     */
    public function validate(array $input)
    {
        $v = $this->v->on($input);
        $v->set('user_name')->asText()->required();
        $v->set('gender')->asText()->required()->in(GenderType::keys());
        $v->set('comment')->asText();

        return $v;
    }
}