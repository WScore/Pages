<?php
namespace WScore\Pages\Base;

/**
 * Class Matcher
 * @package Tuum\Router\Tuum
 *
 * pattern:
 *
 *   (method-name:)/path/{parameter-name(:type)}*
 *
 * - (method-name:) to match against method.
 *   if omitted, any method will match.
 *
 * - {parameter-name} is variable whose name must be [_0-9a-zA-Z]+
 *   and default to value of [-_0-9a-zA-Z]+.
 *
 * - specify parameter type as id:i.
 *   only 'i' is supported for integer ([0-9]).
 *
 * - end route with '*' to say any trailing route.
 *   returning as 'trailing'.
 *
 * if you do not like the static method, try,
 *
 *         (new Matcher)($route, $path, $method);
 *
 * this should do, if PHP can parse it...
 *
 */
class Matcher
{
    /**
     * available parameter types.
     *
     * @var array
     */
    public static $types = [
        'n' => '[0-9]',       // numeric
        'i' => '[-0-9]',      // integer
        'a' => '[a-zA-Z]'     // ascii
    ];

    /**
     * verifies if route pattern, $route, matches against $path and $method.
     *
     * @param string $route
     * @param string $path
     * @param string $method
     * @return array
     */
    public static function verify($route, $path, $method=null)
    {
        /*
         * check method comparison, if method is specified.
         */
        $routeMethod = $method;
        if (strpos($route, ':') !== false && substr($route, 0, 1) !== '/' ) {
            list($routeMethod, $route) = explode( ':', $route, 2 );
            $routeMethod = strtolower($routeMethod);
            if ($method === '*') {
                // no matching against method!
            }
            elseif (strcmp(strtolower($method), $routeMethod)) {
                return [];
            }
        }
        $route = '/'.ltrim($route, '/');
        $path  = '/'.ltrim($path, '/');
        /*
         * now verify route against the path.
         */
        // replace {name} => (?P<name>[-_0-9a-zA-Z]+)
        $route = preg_replace_callback(
            '/{([_:*0-9a-zA-Z]+)}/',
            [self::class,'identifier'],
            $route
        );
        if(substr($route, -1) === '*') {
            $route  = substr($route, 0, -1 ) . '(.*)';
        }
        // now match the route against the path.
        if (preg_match('#^'.$route.'$#', $path, $matches)) {
            if(isset($matches['pathInfo'])) { // with *
                if(!$matches['pathInfo']) {
                    $matches['basePath'] = $matches[0];
                } else {
                    $matches['basePath'] = substr($path, 0, -strlen($matches['trailing']));
                }
            }
            $matches['method'] = $routeMethod;
            return $matches;
        }
        return [];
    }

    /**
     * a callback method for converting identifier in
     * route pattern to regex's one.
     *
     * i.e. {id} -> (?P<id>[\w]}).
     *
     * @param array $match
     * @return string
     */
    public static function identifier(array $match)
    {
        $name = $match[1]; // this should be the matched name.
        if($name === '*') {
            return '(?P<pathInfo>.*)';
        }
        $type = '[-_0-9a-zA-Z]';
        if (strpos($name, ':') !== false) {
            list($name, $type) = explode(':', $name, 2);
            if(isset(self::$types[$type])) {
                $type = self::$types[$type];
            }
        }
        return "(?P<{$name}>{$type}+)";
    }
}