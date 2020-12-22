<?php


namespace WScore\Pages\Mailer;


class Mailer
{
    protected $options = array(
        'From' => '',
        'Reply-To' => '',
        'Bcc' => [],
        'Cc' => [],
        'X-Mailer' => 'php',
    );

    public function __construct()
    {
        mb_language("uni");
        mb_internal_encoding("UTF-8");
        $this->options['From'] = 'invalid@' . $_SERVER['SERVER_NAME'];
        $this->options['Reply-To'] = 'invalid@' . $_SERVER['SERVER_NAME'];
    }

    /**
     * @param string $to
     * @param null|string $name
     * @return string
     */
    public function makeName($to, $name = null)
    {
        if (!$name) {
            return $to;
        }
        $name = mb_encode_mimeheader($name);
        return "{$name} <{$to}>";
    }

    /**
     * @param string $to
     * @param null|string $name
     * @return $this
     */
    public function from($to, $name = null)
    {
        $this->options['From'] = $this->makeName($to, $name);
        return $this;
    }

    /**
     * @param string $to
     * @param null|string $name
     * @return $this
     */
    public function replyTo($to, $name = null)
    {
        $this->options['Reply-To'] = $this->makeName($to, $name);
        return $this;
    }

    /**
     * @param string $to
     * @param null|string $name
     * @return $this
     */
    public function cc($to, $name = null)
    {
        $this->options['Cc'][] = $this->makeName($to, $name);
        return $this;
    }

    /**
     * @param string $to
     * @param null|string $name
     * @return $this
     */
    public function bcc($to, $name)
    {
        $this->options['Bcc'][] = $this->makeName($to, $name);
        return $this;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function send($to, $subject, $body)
    {
        $body = str_replace("\r\n", "\n", $body);    // 改行問題
        $option = $this->makeOption();

        return mb_send_mail($to, $subject, $body, $option);
    }

    /**
     * @return string
     */
    private function makeOption()
    {
        $options = $this->options;
        $options['Cc'] = implode(', ', $options['Cc']);
        $options['Bcc'] = implode(', ', $options['Bcc']);

        return implode("\r\n", $options);
    }
}