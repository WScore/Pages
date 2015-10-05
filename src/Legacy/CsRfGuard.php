<?php
namespace WScore\Pages\Legacy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;

/**
 * Class CsRfGuard
 *
 * Guard against CSRF attack using tokens for Legacy PHP Project.
 * applies CSRF token check based on $request.
 *
 * @package WScore\Pages\Legacy
 */
class CsRfGuard
{
    /**
     * @var bool
     */
    private $safe = true;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string
     */
    private $csrf_key = '_token';

    /**
     * @var array
     */
    private $methods = ['POST'];

    /**
     * a callable function to check $request if csrf guard is necessary.
     * function signature is:
     *    function(ServerRequestInterface $request): bool
     *
     * @var null|callable
     */
    private $guardOn = null;

    /**
     * @param ServerRequestInterface $request
     * @param array                  $options
     */
    public function __construct($request, $options = [])
    {
        $this->request = $request;
        $options += [
            'csrf_key' => '_token',
            'methods'  => ['POST'],
            'guardOn'  => null,
        ];
        $this->csrf_key = $options['csrf_key'];
        $this->methods  = $options['methods'];
        $this->guardOn  = $options['guardOn'];
    }

    /**
     * @param ServerRequestInterface $request
     * @param array                  $options
     * @return CsRfGuard
     */
    public static function forge($request, $options = [])
    {
        $guard = new self($request, $options);
        return $guard;
    }

    /**
     * The CSRF Guard
     * the $callback must draw error page.
     *
     * @param callable $callback
     * @return $this
     */
    public function guard(
        callable $callback
    ) {
        // set up session.
        $session       = RequestHelper::getSessionMgr($this->request);
        $this->request = $this->request
            ->withAttribute('csrf_value', $session->getToken())
            ->withAttribute('csrf_name', $this->csrf_key);

        // check
        if ($this->needToGuard() && !$this->checkCsRfToken()) {
            $this->safe     = false;
            $this->response = $callback($this->request);
        }
        return $this;
    }

    /**
     * @return bool
     */
    private function needToGuard()
    {
        if (!is_null($this->guardOn)) {
            return call_user_func($this->guardOn, $this->request);
        }
        $method = $this->request->getMethod();
        return in_array($method, $this->methods);
    }

    /**
     * @return bool
     */
    private function checkCsRfToken()
    {
        $posts   = $this->request->getParsedBody();
        $token   = array_key_exists($this->csrf_key, $posts) ? $posts[$this->csrf_key] : '';
        $session = RequestHelper::getSessionMgr($this->request);
        return $session->validateToken($token);
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->safe;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return $this
     */
    public function emitAndExitOnFail()
    {
        if ($this->safe) return $this;
        ResponseHelper::emit($this->response);
        exit;
    }
}
