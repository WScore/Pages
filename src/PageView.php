<?php

namespace WScore\Pages;

use App\Application\Middleware\SessionMiddleware;
use Aura\Session\Segment;
use Aura\Session\Session;
use WScore\Pages\View\Data;

/**
 * manages the state of a page (i.e. view).
 *
 * Class PageView
 */
class PageView
{
    const SUCCESS = '200';
    const ERROR = '400';
    const CRITICAL = '500';

    const CSRF_TOKEN = '_csrf_token';

    /**
     * for messages.
     *
     * @var bool
     */
    protected $error = self::SUCCESS;

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
     * @var string
     */
    private $viewFile;

    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     * @param Segment $segment
     * @param string $viewRoot
     */
    public function __construct($session, $segment, $viewRoot)
    {
        $this->viewRoot = $viewRoot;
        $this->segment = $segment;
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
     * @return string[]
     */
    public function getFlashMessages()
    {
        $messages = $this->segment->getFlash('messages');
        return (array) ($messages ? $messages : []);
    }

    /**
     * @return string[]
     */
    public function getFlashNotices()
    {
        $messages = $this->segment->getFlash('notices');
        return (array) ($messages ? $messages : []);
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
        $_view = $this->getContents();
        /** @noinspection PhpIncludeInspection */
        return include $this->viewRoot . '/' . $this->viewFile;
    }

    public function alert()
    {
        $html = '';
        // show error from flash.
        foreach ($this->getFlashNotices() as $msg) {
            $html .= $this->alertDiv($msg, self::ERROR);
        }
        // show message from flash.
        foreach ($this->getFlashMessages() as $msg) {
            $html .= $this->alertDiv($msg, self::SUCCESS);
        }
        // show message
        $html .= $this->alertDiv($this->message, $this->error);

        return $html;
    }

    private function alertDiv($msg, $errLevel)
    {
        if (!$msg) return '';

        $classByLevel = [
            self::CRITICAL => 'alert alert-danger',
            self::ERROR => 'alert alert-danger',
            self::SUCCESS => 'alert alert-success',
        ];
        $class = isset($classByLevel[$errLevel]) ? $classByLevel[$errLevel] : 'alert alert-danger';

        return "<div class=\"{$class}\">\n{$msg}\n</div>";
    }
}