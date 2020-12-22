<?php


namespace WScore\Pages\View;


class Values
{
    /**
     * @var array
     */
    private $values;
    /**
     * @var array
     */
    private $errors;

    public function __construct(array $values, array $errors = [])
    {
        $this->values = $values;
        $this->errors = $errors;
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

    /**
     * @param string $key
     * @return string
     */
    public function html($key)
    {
        $html = htmlspecialchars($this->getRaw($key), ENT_QUOTES, 'UTF-8');
        if ($error = $this->getRawError($key)) {
            $html .= $this->error($key);
        }
        return $html;
    }
}