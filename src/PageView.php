<?php

namespace WScore\Pages;

use Aura\Session\Session;
use WScore\Pages\View\Data;

/**
 * manages the state of a page (i.e. view).
 *
 * Class PageView
 */
class PageView
{
    const ERROR = '400';
    const CRITICAL = '500';

    const CSRF_TOKEN = '_csrf_token';

    /**
     * for messages.
     *
     * @var bool
     */
    protected $error = false;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var array
     */
    protected $contents = array();

    /**
     * @var string
     */
    private $viewRoot;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $viewFile;

    /**
     * @param Session $session
     * @param string $viewRoot
     */
    public function __construct($session, $viewRoot)
    {
        $this->viewRoot = $viewRoot;
        $this->session = $session;
    }

    // +----------------------------------------------------------------------+
    //  Response and locations
    //  a bit like a Response class. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $url
     */
    public function location($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * @param string $viewFile
     * @param array $contents
     */
    public function setRender($viewFile, $contents = [])
    {
        if (!$viewFile) {
            return;
        }
        $this->viewFile = $viewFile;
        if (is_array($contents)) {
            $this->contents = $contents + $this->contents;
        }
    }

    /**
     * @return Data
     */
    public function getContents()
    {
        $this->contents[PageView::CSRF_TOKEN] = $this->session->getCsrfToken()->getValue();
        return new Data($this, $this->contents);
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    public function setError($message)
    {
        $this->error = self::ERROR;
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    public function setCritical($message)
    {
        $this->error = self::CRITICAL;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error >= self::ERROR;
    }

    /**
     * @return bool
     */
    public function isCritical()
    {
        return $this->error >= self::CRITICAL;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function render()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $view = $this->getContents();
        /** @noinspection PhpIncludeInspection */
        return include $this->viewRoot . '/' . $this->viewFile;
    }
}