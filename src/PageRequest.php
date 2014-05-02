<?php
namespace WScore\Pages;

class PageRequest
{
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
        return $this->getData( $key, $_REQUEST, $default );
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

    /**
     * @param null|string $name
     * @return string
     */
    public function getMethod( $name='_method' )
    {
        $http_method = $_SERVER['REQUEST_METHOD'];
        $method = $this->get( $name, $http_method );
        $method = strtolower( $method );
        return $this->safeCode( $method );
    }

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
}