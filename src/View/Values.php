<?php


namespace WScore\Pages\View;


class Values
{
    /**
     * @var array
     */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getRaw($key, $default = '')
    {
        return array_key_exists($key, $this->values)
            ? $this->values[$key]
            : $default;
    }

    /**
     * @param string $key
     * @return string
     */
    public function html($key)
    {
        return $this->e($this->getRaw($key));
    }

    /**
     * @param string $string
     * @return string
     */
    public function e($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}