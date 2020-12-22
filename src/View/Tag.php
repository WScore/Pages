<?php


namespace WScore\Pages\View;


class Tag
{
    /**
     * @var string
     */
    private $tagName;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $contents = [];

    /**
     * @var bool
     */
    private $hasCloseTag = false;

    /**
     * @param string $tagName
     */
    public function __construct($tagName)
    {
        $this->tagName = $tagName;
    }

    /**
     * @param string $tagName
     * @return Tag
     */
    public static function create($tagName)
    {
        return new self($tagName);
    }

    /**
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @return Tag
     */
    public function hasCloseTag()
    {
        $this->hasCloseTag = true;
        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $value
     * @param string $conn
     * @return $this
     */
    public function addAttribute($key, $value = true, $conn = ' ')
    {
        if ($value === true) {
            $value = $key;
        }
        if (isset($this->attributes[$key]) && $this->attributes[$key]) {
            $this->attributes[$key] .= $conn . $value;
            return $this;
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function delAttribute($key)
    {
        unset($this->attributes[$key]);
        return $this;
    }

    /**
     * @param mixed ...$contents
     * @return $this
     */
    public function addContents(...$contents)
    {
        foreach ($contents as $content) {
            if (!$content) continue;
            $this->contents[] = $content;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->htmlToString();
    }

    /**
     * @return string
     */
    private function htmlToString()
    {
        $attributes = $this->makeAttributes();
        if ($this->hasCloseTag) {
            $contents = implode("\n", $this->contents);
            if (count($this->contents) > 1) {
                $contents = "\n" . $contents . "\n";
            }
            return "<{$attributes}>{$contents}</{$this->tagName}>";
        }
        return "<{$attributes}>";
    }

    /**
     * @return string
     */
    private function makeAttributes()
    {
        $list = [$this->tagName];
        foreach ($this->attributes as $key => $attribute) {
            $attr = htmlspecialchars($attribute, ENT_QUOTES);
            if (!$attr) continue;
            $list[] = "{$key}=\"{$attr}\"";
        }
        return implode(' ', $list);
    }
}