<?php
namespace WScore\Pages;

/**
 * Class Factory
 * a simple, quick, and dumb way to di?
 *
 * @package WScore\Pages
 */
class Factory
{
    static $request  = '\WScore\Pages\Request';
    static $pageView = '\WScore\Pages\PageView';
    static $session  = '\WScore\Pages\Session';

    /**
     * @return Request
     */
    public static function getRequest()
    {
        return new static::$request();
    }

    /**
     * @return PageView
     */
    public static function getPageView()
    {
        return new static::$pageView();
    }

    /**
     * @return Session
     */
    public static function getSession()
    {
        /** @var Session $class */
        $class = static::$session;
        return $class::getInstance();
    }

    /**
     * @param ControllerAbstract $controller
     * @return Dispatch
     */
    public static function getDispatch( $controller )
    {
        $dispatch = new Dispatch(
            static::getRequest(),
            static::getPageView(),
            static::getSession()
        );
        $dispatch->setController( $controller );
        return $dispatch;
    }
}