<?php
namespace WScore\Pages;

class PageSession
{
    const FLASH_ID = '_flash';
    const TOKEN_ID = '_token';
    const MAX_TOKEN = 20;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $flashed = array();

    // +-------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if( !isset( $_SESSION ) ) {
            session_start();
        }
        /** @var PageSession $session */
        $session = new static();
        $session->setData( $_SESSION );
        return $session;
    }

    /**
     * @param $data
     */
    public function setData( &$data )
    {
        $this->data = &$data;
        // for flash data.
        if( !isset( $this->data[ self::FLASH_ID ] ) ) {
            $this->data[ self::FLASH_ID ] = array();
        }
        $this->flashed  = $this->data[ self::FLASH_ID ];
        $this->data[ self::FLASH_ID ] = array();
    }
    // +----------------------------------------------------------------------+
    //  set/get/del variables to Session.
    // +----------------------------------------------------------------------+
    /**
     * @param $name
     * @return mixed
     */
    public function get( $name )
    {
        if( array_key_exists( $name,  $this->data ) ) {
            return $this->data[ $name ];
        }
        if( array_key_exists( $name,  $this->flashed ) ) {
            return $this->flashed[ $name ];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set( $name, $value )
    {
        $this->data[ $name ] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function flash( $name, $value )
    {
        $this->data[ self::FLASH_ID ][ $name ] = $value;
        $this->del( $name ); // just in case.
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function del( $name )
    {
        if( array_key_exists( $name,  $this->data ) ) {
            unset( $this->data[ $name ] );
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->data = array();
        return $this;
    }
    // +----------------------------------------------------------------------+
    //  managing token for C.S.R.F.
    // +----------------------------------------------------------------------+
    /**
     * @return string
     */
    public function pushToken()
    {
        $token = md5( 'session.dumb' . time() . mt_rand(1,100*100) . __DIR__ );
        $this->_pushToken( $token );
        return $token;
    }

    /**
     * @param $token
     */
    protected function _pushToken( $token )
    {
        if( !isset( $this->data[ static::TOKEN_ID ] ) ) {
            $this->data[ static::TOKEN_ID ] = array();
        }
        $this->data[ static::TOKEN_ID ][] = $token;
        if( count( $this->data[ static::TOKEN_ID ] ) <= static::MAX_TOKEN ) {
            return;
        }
        $num_remove = count( $this->data[ static::TOKEN_ID ] ) - static::MAX_TOKEN;
        $this->data[ static::TOKEN_ID ] =
            array_slice( $this->data[ static::TOKEN_ID ], $num_remove );
    }

    /**
     * @param $token
     * @return bool
     */
    public function verifyToken( $token )
    {
        if( !isset( $this->data[ static::TOKEN_ID ] ) ||
            empty( $this->data[ static::TOKEN_ID ] ) ) {
            return false;
        }
        if( !in_array( $token, $this->data[ static::TOKEN_ID ] ) ) {
            return false;
        }
        // The token is in the list. remove it from the list.
        $key = array_search( $token, $this->data[ static::TOKEN_ID ] );
        unset( $this->data[ static::TOKEN_ID ][ $key ] );
        $this->data[ static::TOKEN_ID ] = array_values( $this->data[ static::TOKEN_ID ] );
        return true;
    }
    // +----------------------------------------------------------------------+
}