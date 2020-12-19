<?php
namespace WScore\Pages\View;

use Aura\Session\Session;
use WScore\Pages\PageView;

/**
 * manages the state of a page (i.e. view).
 *
 * Class PageView
 */
class Data
{
    /**
     * @var array
     */
    protected $contents    = array();
    /**
     * @var PageView
     */
    private $view;

    /**
     * @param PageView $view
     * @param array $contents
     */
    public function __construct($view, $contents)
    {
        $this->view = $view;
        $this->contents = $contents;
    }

    // +----------------------------------------------------------------------+
    //  managing variables for a page view. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $key
     * @param mixed $value
     */
    public function set( $key, $value )
    {
        $this->contents[ $key ] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists( $key )
    {
        return array_key_exists( $key, $this->contents );
    }

    /**
     * @param string     $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get( $key=null, $default=null )
    {
        if( !$key ) return $this->contents;
        if( $this->exists($key) ) {
            return $this->contents[$key];
        }
        return $default;
    }

    // +----------------------------------------------------------------------+
    //  managing html stuff.
    // +----------------------------------------------------------------------+
    public function h( $key ) {
        $value = $this->get($key);
        if( is_object($value) && method_exists( $value, '__toString') ) {
            $value = (string) $value;
        }
        if( is_string( $value ) ) {
            $value = htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
        }
        return $value;
    }

    /**
     * @param string $key
     * @param string|null $name
     * @return string
     */
    public function makeHiddenTag( $key, $name=null )
    {
        if( !$this->exists($key) ) {
            return '';
        }
        $value = $this->get( $key );
        if( !$name ) $name = $key;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\" />";
    }

    /**
     * @return mixed|string
     */
    public function makeCsRfToken()
    {
        return $this->makeHiddenTag( PageView::CSRF_TOKEN );
    }

    // +----------------------------------------------------------------------+
    //  managing errors and messages.
    //  should this be a different class?
    // +----------------------------------------------------------------------+

    /**
     * @return string
     */
    public function alert()
    {
        $html = $this->view->getMessage();
        if( !$html ) return $html;
        if( $this->view->isCritical() ) {
            $html = "<strong>Critical Error:</strong><br/>\n{$html}";
            $class = 'alert alert-danger';
        } elseif( $this->view->isError() ) {
            $html = "<strong>Error:</strong><br/>\n{$html}";
            $class = 'alert alert-danger';
        } else {
            $class = 'alert alert-success';
        }
        $html  = "<div class=\"{$class}\">\n{$html}\n</div>";
        return $html;
    }

    public function render()
    {

    }
}