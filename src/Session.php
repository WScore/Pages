<?php
namespace WScore\Pages;

class Session
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

    /**
     * @var string
     */
    protected $undo_token;

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
        if( self::isSessionStarted() ) {
            session_start();
        }
        /** @var Session $session */
        $session = new static();
        $session->setData( $_SESSION );
        return $session;
    }

    /**
     * use new function from:
     * http://www.php.net/manual/en/function.session-status.php
     * 
     * @return bool
     */
    public static function isSessionStarted()
    {
        if ( php_sapi_name() === 'cli' ) {
            return FALSE;
        }
        if ( function_exists( 'session_status' ) ) { // new function (PHP >=5.4.0)
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        }
        return session_id() === '' ? FALSE : TRUE;
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
        if( !is_array( $this->data[ static::TOKEN_ID ] ) ) {
            $this->data[ static::TOKEN_ID ] = array($this->data[ static::TOKEN_ID ]);
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
        $this->undo_token = $token;
        return true;
    }

    /**
     *
     */
    public function undoToken()
    {
        if( !$this->undo_token ) return;
        $this->_pushToken( $this->undo_token );
    }
    // +----------------------------------------------------------------------+
}