<?php
namespace WScore\Pages\Mailer;

class MailJa
{
    /**
     * @var string
     */
    protected $mailTo;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $options = array(
        'From:' => '',
        'Reply-To:' => '',
        'Bcc:' => '',
        'Cc:' => '',
        'X-Mailer:' => 'php/5.x',
    );

    protected static $mailBy = 'JIS';

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct()
    {
        if( isset( $_SERVER['SERVER_NAME'] ) ) {
            $this->from( 'invalid@'.$_SERVER['SERVER_NAME'] );
        }
        $this->setup();
    }

    protected function setup()
    {
        if( !mb_internal_encoding() ) {
            mb_internal_encoding('UTF-8');
        }
        if( static::$mailBy == 'JIS' ) {
            mb_language('Japanese');
        } else {
            mb_language('uni');
        }
    }

    protected function encode( $value )
    {
        switch( static::$mailBy ) {
            case 'JIS':
                $en = 'ISO-2022-JP';
                break;
            case 'Utf8':
                $en = 'UTF-8';
                break;
            case 'MS':
                $en = 'ISO-2022-JP-MS';
                break;
            default:
                throw new \InvalidArgumentException( "Invalid mailBy type:". static::$mailBy );
        }
        return mb_encode_mimeheader( $value, $en, 'UTF-8' );
    }

    /**
     * sends mail in UTF-8
     */
    public static function mailByUtf8() {
        static::$mailBy = 'Utf8';
    }

    /**
     * sends mail in JIS (ISO-2022-JP).
     */
    public static function mailByJIS() {
        static::$mailBy = 'JIS';
    }

    /**
     * sends mail in ISO-2022-JP-MS.
     */
    public static function mailByMs() {
        static::$mailBy = 'MS';
    }

    // +----------------------------------------------------------------------+
    //  set up mail content
    // +----------------------------------------------------------------------+
    /**
     * @param string $to
     * @param string $name
     * @return string
     */
    public function name( $to, $name )
    {
        if( !$name ) {
            return $to;
        }
        $name = $this->encode( $name );
        return "{$name}<{$to}>";
    }

    /**
     * @param string $mailTo
     * @param string $name
     * @return $this
     */
    public function to( $mailTo, $name=null )
    {
        $mailTo = $this->name( $mailTo, $name );
        if( $this->mailTo ) {
            $mailTo = ','.$mailTo;
        }
        $this->mailTo .= $mailTo;
        return $this;
    }

    /**
     * @param string|array $cc
     * @return $this
     */
    public function cc( $cc )
    {
        $this->setCarbon( $cc, 'Cc' );
        return $this;
    }

    /**
     * @param $bcc
     * @return $this
     */
    public function bcc( $bcc )
    {
        $this->setCarbon( $bcc, 'Bcc' );
        return $this;
    }

    /**
     * @param $cc
     * @param $as
     */
    protected function setCarbon( $cc, $as )
    {
        if( is_array( $cc ) ) {
            $copyTo = array();
            foreach( $cc as $mail ) {
                $copyTo[] = $mail;
            }
            $cc = implode(',', $copyTo );
        }
        if( !$cc ) return;
        $as .= ':';
        if( !isset( $this->options[$as] ) ) {
            $this->options[$as] = '';
        }
        if( $this->options[$as] ) {
            $this->options[$as] .= ','.$cc;
        } else {
            $this->options[$as] = $cc;
        }
    }

    /**
     * @param $from
     * @param $name
     * @return $this
     */
    public function from( $from, $name=null )
    {
        $this->options['From'] = $this->name( $from, $name );
        if( !$this->options['Reply-To:'] ) {
            $this->options['Reply-To:'] = $from;
        }
        return $this;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function subject( $subject )
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $body
     */
    public function body( $body )
    {
        $body    = str_replace( "\r\n", "\n", $body );	// 改行問題
        $this->body = $body;
    }

    // +----------------------------------------------------------------------+
    //  sending mails
    // +----------------------------------------------------------------------+
    /**
     * @param null $by
     * @return bool
     */
    public function send($by=null)
    {
        if( !$by ) $by = static::$mailBy;
        $by = 'send'.$by;
        return $this->$by();
    }

    /**
     * @return string
     */
    protected function getOption()
    {
        return implode("\n", $this->options );
    }

    /**
     * sends mail using mb_send_mail in JIS format.
     * @return bool
     */
    protected function sendJIS()
    {
        $body    = $this->encode( $this->body );
        $option  = $this->getOption();
        $success = mb_send_mail( $this->mailTo, $this->subject, $body, $option );
        return $success;
    }

    /**
     * sends mail using mb_send_mail in JIS-MS format.
     * @return bool
     */
    protected function sendMs()
    {
        $this->options['Content-Type:'] = 'text/plain; charset="ISO-2022-JP"';
        $option  = $this->getOption();
        $subject = $this->encode( $this->subject );
        $body    = $this->encode( $this->body );
        $success = mb_send_mail( $this->mailTo, $subject, $body, $option );
        return $success;
    }

    /**
     * sends mail using mb_send_mail in UTF-8 format.
     * @return bool
     */
    protected function sendUtf8()
    {
        $option  = $this->getOption();
        $success = mb_send_mail( $this->mailTo, $this->subject, $this->body, $option );
        return $success;
    }
    // +----------------------------------------------------------------------+
}