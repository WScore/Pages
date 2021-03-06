<?php


namespace WScore\Pages\View;


class Form
{
    /**
     * @var Values
     */
    private $values;

    /**
     * @var array
     */
    private $errors;

    public function __construct(Values $values, array $errors = [])
    {
        $this->values = $values;
        $this->errors = $errors;
    }

    /**
     * @param string $type
     * @param string $key
     * @return Tag
     */
    public function makeInput($type, $key)
    {
        $value = $this->values->getRaw($key);
        return Tag::create('input')
            ->addAttribute('type', $type)
            ->addAttribute('name', $key)
            ->addAttribute('id', $key)
            ->addAttribute('value', $value);
    }

    /**
     * @param string $type
     * @param array $choices
     * @param string $key
     * @return Tag
     */
    public function listInputs($type, array $choices, $key)
    {
        $values = (array) $this->values->getRaw($key);
        $list = Tag::create('div')
            ->addAttribute('class', 'choice-list')
            ->hasCloseTag();
        foreach ($choices as $k => $val) {
            $input = Tag::create('input')
                ->addAttribute('type', $type)
                ->addAttribute('name', $key)
                ->addAttribute('value', $k);
            if (in_array($k, $values)) {
                $input->addAttribute('checked', 'checked');
            }
            $label = Tag::create('label')
                ->addContents($input, $val)
                ->hasCloseTag()
                ->addAttribute('class', 'choice-items');
            $list->addContents($label);
        }
        return $list;
    }

    /**
     * @param array $choices
     * @param string $key
     * @return Tag
     */
    public function makeSelect(array $choices, $key)
    {
        $values = (array) $this->values->getRaw($key);
        $select = Tag::create('select')
            ->addAttribute('name', $key);
        foreach ($choices as $key => $val) {
            $option = Tag::create('option')
                ->addAttribute('value', $val);
            if (in_array($val, $values)) {
                $option->addAttribute('checked', 'checked');
            }
            $select->addContents($option);
        }
        return $select;
    }

    public function makeTextArea($key)
    {
        $value = $this->values->getRaw($key);
        return Tag::create('textarea')
            ->addAttribute('name', $key)
            ->addAttribute('id', $key)
            ->addContents($value)
            ->hasCloseTag();
    }

    public function getRawError($key)
    {
        return array_key_exists($key, $this->errors)
            ? $this->errors[$key]
            : '';
    }

    /**
     * @param string $message
     * @return string
     */
    protected function makeErrorMessage($message)
    {
        return "<span class=\"error\">{$message}</span>";
    }

    /**
     * @param string $key
     * @return string
     */
    public function error($key)
    {
        if ($error = $this->getRawError($key)) {
            return $this->makeErrorMessage($error);
        }
        return '';
    }
}