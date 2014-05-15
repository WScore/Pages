<?php
namespace WScore\Pages;

/**
 * manages the state of a page (i.e. view).
 *
 * Class PageView
 */
class PageView implements \ArrayAccess
{
    const ERROR    = '400';
    const CRITICAL = '500';

    /**
     * for messages. 
     * 
     * @var bool
     */
    protected $error   = false;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * 
     * @var array
     */
    protected $contents    = array();

    // +----------------------------------------------------------------------+
    //  Response and locations
    //  a bit like a Response class. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $url
     */
    public function location( $url )
    {
        header( "Location: {$url}" );
        exit;
    }

    // +----------------------------------------------------------------------+
    //  managing variables for a page view. 
    // +----------------------------------------------------------------------+
    /**
     * @param $key
     * @param $value
     */
    function set( $key, $value )
    {
        $this->contents[ $key ] = $value;
    }
    /** outdated method. */
    function add( $k, $v) {$this->set($k,$v);}

    /**
     * @param $key
     * @return bool
     */
    function exists( $key )
    {
        return array_key_exists( $key, $this->contents );
    }

    /**
     * @param array|mixed $contents
     */
    function assign( $contents )
    {
        if( !$contents ) return;
        if( is_array( $contents ) ) {
            $this->contents = $contents + $this->contents;
        }
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    function get( $key, $default=null )
    {
        if( $this->exists($key) ) {
            return $this->contents[$key];
        }
        return $default;
    }

    /**
     * @param $key
     * @return array
     */
    function collection( $key )
    {
        $got = $this->get( $key );
        if( !$got ) {
            return array();
        }
        if( !is_array( $got ) ) {
            return array( $got );
        }
        return $got;
    }

    /**
     * @param string $key
     * @param string|array $value
     * @return bool
     */
    function is( $key, $value )
    {
        $real = $this->get( $key );
        if( is_array( $value ) ) {
            return in_array( $real, $value );
        }
        return (string) $real == (string) $value;
    }
    // +----------------------------------------------------------------------+
    //  managing html stuff.
    // +----------------------------------------------------------------------+
    /**
     * @param string $key
     * @return string
     */
    function getHidden( $key )
    {
        if( $this->exists($key) ) {
            $value = $this->get( $key );
            return '<input type="hidden" name="' . "{$key}\" value=\"{$value}\" />";
        }
        return '';
    }

    /**
     * @param $method
     */
    function setCurrentMethod( $method )
    {
        $this->set( '_current_method', $method );
    }

    /**
     * @return string|null
     */
    function getCurrentMethod()
    {
        return $this->get( '_current_method' );
    }

    /**
     * @param $method
     */
    function setMethod($method)
    {
        $this->set( '_method', $method );
    }

    /**
     * @param bool $tag
     * @return string
     */
    function getMethod( $tag=true )
    {
        if( !$method = $this->get( '_method' ) ) return '';
        if( $tag ) {
            $method = $this->getHidden( '_method' );
        }
        return $method;
    }

    /**
     * @param bool $tag
     * @return mixed|string
     */
    function getToken( $tag=true )
    {
        if( !$this->exists( Session::TOKEN_ID ) ) return '';
        if( $tag ) {
            return $this->getHidden( Session::TOKEN_ID );
        }
        return $this->get( Session::TOKEN_ID );
    }

    /**
     * @param string $value
     * @param string $key
     */
    function setButton($value, $key='_buttonValue')
    {
        $this->set( $key, $value);
    }

    /**
     * @param string $key
     * @return string
     */
    function getButton($key='_buttonValue')
    {
        if( !$value = $this->get($key) ) return '';
        return '<input type=' . "\"submit\" value=\"{$value}\" />";
    }

    /**
     * @param $type
     */
    function setSubButton($type)
    {
        $this->set( '_subButtonType', $type );
    }

    /**
     * @param null $key
     * @return string
     */
    function getSubButton($key=null)
    {
        if( !$key ) $key = '_subButtonType';
        $html = '';
        $type = $this->get($key);
        switch( $type ) {
            case 'reset':
                $html = '<input type="reset" value="リセット" />';
                break;
            case 'back':
                $html = '<input type="button" value="戻る" onclick="history.back();" />';
                break;
            default:
                break;
        }
        return $html;
    }

    // +----------------------------------------------------------------------+
    //  managing errors and messages.
    //  should this be a different class?
    // +----------------------------------------------------------------------+
    /**
     * @param string $message
     */
    function message( $message )
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    function error( $message )
    {
        $this->error = self::ERROR;
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    function critical( $message )
    {
        $this->error = self::CRITICAL;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    function isError()
    {
        return $this->error >= self::ERROR;
    }

    /**
     * @return bool
     */
    function isCritical()
    {
        return $this->error >= self::CRITICAL;
    }

    /**
     * @return string
     */
    function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    function alert()
    {
        $html = '';
        if( !$this->message ) return $html;
        if( $this->isError() ) {
            $html .= "<strong>Error:</strong><br/>\n";
            $class = 'alert alert-danger';
        } else {
            $class = 'alert alert-success';
        }
        $html .= $this->message;
        $html  = "<div class=\"{$class}\">{$html}</div>";
        return $html;
    }
    // +----------------------------------------------------------------------+
    //  for ArrayAccess
    // +----------------------------------------------------------------------+
    /**
     * Whether a offset exists
     */
    public function offsetExists( $offset )
    {
        return array_key_exists( $offset, $this->contents );
    }

    /**
     * Offset to retrieve
     */
    public function offsetGet( $offset )
    {
        return array_key_exists( $offset, $this->contents ) ? $this->contents[$offset] : null;
    }

    /**
     * Offset to set
     */
    public function offsetSet( $offset, $value )
    {
        $this->contents[$offset] = $value;
    }

    /**
     * Offset to unset
     */
    public function offsetUnset( $offset )
    {
        if( $this->offsetExists($offset) ) {
            unset( $this->contents[$offset] );
        }
    }
}