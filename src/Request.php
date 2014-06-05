<?php
namespace WScore\Pages;

class Request
{
    /**
     * key name for overwriting http method.
     *
     * @var string
     */
    protected $method_name = '_method';

    /**
     * request data ($_REQUEST, usually). set to false if not set yet.
     * @var array|bool
     */
    protected $request = false;

    /**
     * @var bool|array
     */
    protected $server = false;

    // +----------------------------------------------------------------------+
    //  construction and data
    // +----------------------------------------------------------------------+
    public function __construct( $request=null, $server=null )
    {
        $this->setRequest( $request );
        $this->setServer(  $server );
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setRequest( $data )
    {
        if( !$data ) {
            $this->request = & $_REQUEST;
        } else {
            $this->request = $data;
        }
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setServer( $data )
    {
        if( !$data ) {
            $this->server = & $_SERVER;
        } else {
            $this->server = $data;
        }
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  passing data to next request
    // +----------------------------------------------------------------------+
    /**
     * @param array $data
     * @return string
     */
    public function pack( $data )
    {
        $data = serialize( $data );
        $data = base64_encode( $data );
        return $data;
    }

    /**
     * @param string$data
     * @return array
     */
    public function unpack( $data )
    {
        $data = base64_decode( $data );
        $data = unserialize( $data );
        return $data;
    }

    /**
     * @param null|array $post
     * @return string
     */
    public function packPost( $post=null )
    {
        if( !$post ) $post = $_POST;
        $saved = $this->pack( $post );
        return $saved;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function loadPost( $name )
    {
        if( !$info = $this->get( $name ) ) return false;
        $data = $this->unpack( $info );
        $this->request = array_merge( $this->request, $data );
        return true;
    }

    // +----------------------------------------------------------------------+
    //  getting data from request.
    // +----------------------------------------------------------------------+
    /**
     * @param string $key
     * @param string $data
     * @param null|mixed $default
     * @return mixed
     */
    protected function getData( $key, $data, $default=null )
    {
        return array_key_exists( $key, $data ) ? $data[$key] : $default;
    }

    /**
     * @param $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get( $key, $default=null )
    {
        return array_key_exists( $key, $this->request ) ? $this->request[$key] : $default;
    }

    /**
     * @param $key
     * @param null $default
     * @return null|string
     */
    public function getCode( $key, $default=null )
    {
        return $this->safeCode( $this->get( $key, $default ) );
    }

    /**
     * @param $key
     * @param null $default
     * @return null|string
     */
    public function getString( $key, $default=null )
    {
        return $this->safeString( $this->get( $key, $default ) );
    }

    /**
     * @param $string
     * @return null|string
     */
    public function safeString( $string )
    {
        if( !mb_check_encoding( $string, 'UTF-8' ) ) {
            return null;
        }
        return $string;
    }

    /**
     * @param $code
     * @return null|string
     */
    public function safeCode( $code )
    {
        $code = $this->safeString( $code );
        if( preg_match( '/^[-_0-9a-zA-Z]*$/', $code ) ) {
            return $code;
        }
        return null;
    }

    // +----------------------------------------------------------------------+
    //  request method
    // +----------------------------------------------------------------------+
    /**
     * @param $name
     */
    public function setMethodName( $name )
    {
        $this->method_name = $name;
    }

    /**
     * @param null|string $name
     * @return string
     */
    public function getMethod( $name=null )
    {
        
        $http_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD']: 'get';
        if( !$name ) $name = $this->method_name;
        $method = $this->get( $name, $http_method );
        $method = strtolower( $method );
        return $this->safeCode( $method );
    }

    // +----------------------------------------------------------------------+
    //  user-agent
    // +----------------------------------------------------------------------+
    /**
     * @param null|string $ua
     * @return bool
     */
    public function isMobile( $ua=null )
    {
        $ua = $ua ?: $_SERVER['HTTP_USER_AGENT'];
        if( false!==strpos( $ua, 'iPhone' ) ) return true; // iPhone
        if( false !== strpos( $ua, 'Android' ) && false!==strpos( $ua, 'Mobile' ) ) {
            return true; // android phone.
        }
        if( false!==strpos( $ua, 'Windows Phone' ) ) return true; // Windows Phone
        return false;
    }

    /**
     * @param null|string $ua
     * @return bool
     */
    public function isTablet( $ua=null )
    {
        $ua = $ua ?: $_SERVER['HTTP_USER_AGENT'];
        if( false!==strpos( $ua, 'iPad' ) ) return true; // iPad
        if( false !== strpos( $ua, 'Android' ) && false===strpos( $ua, 'Mobile' ) ) {
            return true; // android tablet.
        }
        return false;
    }
    // +----------------------------------------------------------------------+
}