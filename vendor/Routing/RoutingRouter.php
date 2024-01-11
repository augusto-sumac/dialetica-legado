<?php

class RoutingRouter
{
    /**
     * The route names that have been matched.
     *
     * @var array
     */
    public static $names = array();

    /**
     * The actions that have been reverse routed.
     *
     * @var array
     */
    public static $uses = array();

    /**
     * All of the routes that have been registered.
     *
     * @var array
     */
    public static $routes = array(
        'GET'    => array(),
        'POST'   => array(),
        'PUT'    => array(),
        'DELETE' => array(),
        'PATCH'  => array(),
        'HEAD'   => array(),
        'OPTIONS' => array(),
    );

    /**
     * All of the "fallback" routes that have been registered.
     *
     * @var array
     */
    public static $fallback = array(
        'GET'    => array(),
        'POST'   => array(),
        'PUT'    => array(),
        'DELETE' => array(),
        'PATCH'  => array(),
        'HEAD'   => array(),
        'OPTIONS' => array(),
    );

    /**
     * The current attributes being shared by routes.
     */
    public static $group;


    /**
     * The number of URI segments allowed as method arguments.
     *
     * @var int
     */
    public static $segments = 5;

    /**
     * The wildcard patterns supported by the router.
     *
     * @var array
     */
    public static $patterns = array(
        '(:num)' => '([0-9]+)',
        '(:any)' => '([a-zA-Z0-9\.\-_%=]+)',
        '(:segment)' => '([^/]+)',
        '(:all)' => '(.*)',
    );

    /**
     * The optional wildcard patterns supported by the router.
     *
     * @var array
     */
    public static $optional = array(
        '/(:num?)' => '(?:/([0-9]+)',
        '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)',
        '/(:segment?)' => '(?:/([^/]+)',
        '/(:all?)' => '(?:/(.*)',
    );

    /**
     * An array of HTTP request methods.
     *
     * @var array
     */
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS');

    /**
     * Register many request URIs to a single action.
     *
     * <code>
     *		// Register a group of URIs for an action
     *		RoutingRouter::share(array(array('GET', '/'), array('POST', '/')), 'home@index');
     * </code>
     *
     * @param  array  $routes
     * @param  mixed  $action
     * @return void
     */
    public static function share($routes, $action)
    {
        foreach ($routes as $route) {
            static::register($route[0], $route[1], $action);
        }
    }

    /**
     * Register a group of routes that share attributes.
     *
     * @param  array    $attributes
     * @param  Closure  $callback
     * @return void
     */
    public static function group($attributes, Closure $callback)
    {
        $old_attributes = static::$group;

        // Pass the oldest attributes to next router
        if (is_array($old_attributes)) {
            foreach ($old_attributes as $name => $value) {
                if ($name == 'prefix') {
                    $prefix = array_get($attributes, $name);
                    $attributes[$name] = str_replace('//', '', implode('/', [
                        $value,
                        $prefix
                    ]));
                } else if ($name == 'before' || $name == 'after') {
                    $event = array_get($attributes, $name);
                    $attributes[$name] = rtrim(implode(',', [
                        $value,
                        $event
                    ]), ',');
                } else if (!isset($attributes[$name])) {
                    $attributes[$name] = $value;
                }
            }
        }

        // Route groups allow the developer to specify attributes for a group
        // of routes. To register them, we'll set a static property on the
        // router so that the register method will see them.
        static::$group = $attributes;

        call_user_func($callback);

        // Once the routes have been registered, we want to set the group to
        // null so the attributes will not be given to any of the routes
        // that are added after the group is declared.
        static::$group = $old_attributes;
    }

    public static function slashUri($uri)
    {
        return '/' . rtrim(ltrim($uri, '/'), '/');
    }

    /**
     * Register a route with the router.
     *
     * <code>
     *		// Register a route with the router
     *		RoutingRouter::register('GET', '/', function() {return 'Home!';});
     *
     *		// Register a route that handles multiple URIs with the router
     *		RoutingRouter::register(array('GET', '/', 'GET /home'), function() {return 'Home!';});
     * </code>
     *
     * @param  string        $method
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function register($method, $route, $action)
    {
        if (ctype_digit($route)) $route = "({$route})";

        if (is_string($route)) $route = explode(', ', $route);

        // If the developer is registering multiple request methods to handle
        // the URI, we'll spin through each method and register the route
        // for each of them along with each URI and action.
        if (is_array($method)) {
            foreach ($method as $http) {
                static::register($http, $route, $action);
            }

            return;
        }

        $trimUri = fn ($s) => '/' . rtrim(ltrim($s, '/'), '/');

        foreach ((array) $route as $uri) {
            // If the URI begins with a splat, we'll call the universal method, which
            // will register a route for each of the request methods supported by
            // the router. This is just a notational short-cut.
            if ($method == '*') {
                foreach (static::$methods as $method) {
                    static::register($method, $route, $action);
                }

                continue;
            }

            if ($uri == '') {
                $uri = '/';
            }

            // If the URI begins with a wildcard, we want to add this route to the
            // array of "fallback" routes. Fallback routes are always processed
            // last when parsing routes since they are very generic and could
            // overload bundle routes that are registered.
            if ($uri[0] == '(' || $uri[0] == '{') {
                $routes = &static::$fallback;
            } else {
                $routes = &static::$routes;
            }

            $uri = static::slashUri($uri);

            if (!is_null(static::$group)) {
                if ($prefix = array_get(static::$group, 'prefix')) {
                    $uri = rtrim(rtrim($prefix, '/') . '/' . ltrim($uri, '/'), '/');
                }

                $uri = static::slashUri($uri);

                if (is_array($action)) {
                    foreach (static::$group as $name => $value) {
                        if ($name == 'before' || $name == 'after') {
                            $event = array_get($action, $name);
                            $action[$name] = rtrim(implode(',', [
                                $value,
                                $event
                            ]), ',');
                        }
                    }
                }
            }

            // If the action is an array, we can simply add it to the array of
            // routes keyed by the URI. Otherwise, we will need to call into
            // the action method to get a valid action array.
            if (is_array($action)) {
                $routes[$method][$uri] = $action;
            } else {
                $routes[$method][$uri] = static::action($action);
            }

            // If a group is being registered, we'll merge all of the group
            // options into the action, giving preference to the action
            // for options that are specified in both.
            if (!is_null(static::$group)) {
                $routes[$method][$uri] += static::$group;
            }

            // If the HTTPS option is not set on the action, we'll use the
            // value given to the method. The secure method passes in the
            // HTTPS value in as a parameter short-cut.
            if (!isset($routes[$method][$uri]['https'])) {
                $routes[$method][$uri]['https'] = false;
            }
        }
    }

    /**
     * Convert a route action to a valid action array.
     *
     * @param  mixed  $action
     * @return array
     */
    protected static function action($action)
    {
        // If the action is a string, it is a pointer to a controller, so we
        // need to add it to the action array as a "uses" clause, which will
        // indicate to the route to call the controller.
        if (is_string($action)) {
            $action = array('uses' => $action);
        }
        // If the action is a Closure, we will manually put it in an array
        // to work around a bug in PHP 5.3.2 which causes Closures cast
        // as arrays to become null. We'll remove this.
        elseif ($action instanceof Closure) {
            $action = array($action);
        }

        return (array) $action;
    }

    /**
     * Find a route by the route's assigned name.
     *
     * @param  string  $name
     * @return array
     */
    public static function find($name)
    {
        if (isset(static::$names[$name])) return static::$names[$name];

        // To find a named route, we will iterate through every route defined
        // for the application. We will cache the routes by name so we can
        // load them very quickly the next time.
        foreach (static::routes() as $method => $routes) {
            foreach ($routes as $key => $value) {
                if (isset($value['as']) and $value['as'] === $name) {
                    return static::$names[$name] = array($key => $value);
                }
            }
        }
    }

    public static function run()
    {
        $route = static::route(request_method(), uri_path());
        return $route && method_exists($route, 'call') ? $route->call() : $route;
    }

    /**
     * Search the routes for the route matching a method and URI.
     *
     * @param  string   $method
     * @param  string   $uri
     * @return RoutingRoute
     */
    public static function route($method, $uri)
    {
        $routes = (array) static::method($method);

        // Of course literal route matches are the quickest to find, so we will
        // check for those first. If the destination key exists in the routes
        // array we can just return that route now.
        if (array_key_exists($uri, $routes)) {
            $action = $routes[$uri];

            return new RoutingRoute($method, $uri, $action);
        }

        $route = static::match($method, $uri);

        // If we can't find a literal match we'll iterate through all of the
        // registered routes to find a matching route based on the route's
        // regular expressions and wildcards.
        if (!is_null($route)) {
            return $route;
        }
    }

    /**
     * Iterate through every route to find a matching route.
     *
     * @param  string  $method
     * @param  string  $uri
     * @return RoutingRoute
     */
    protected static function match($method, $uri)
    {
        foreach (static::method($method) as $route => $action) {
            // We only need to check routes with regular expression since all others
            // would have been able to be matched by the search for literal matches
            // we just did before we started searching.
            if (str_contains($route, '(') || str_contains($route, '{')) {
                $pattern = '#^' . static::wildcards($route) . '$#u';

                // If we get a match we'll return the route and slice off the first
                // parameter match, as preg_match sets the first array item to the
                // full-text match of the pattern.
                if (preg_match($pattern, $uri, $parameters)) {
                    return new RoutingRoute($method, $route, $action, array_slice($parameters, 1));
                }
            }
        }
    }

    /**
     * Translate route URI wildcards into regular expressions.
     *
     * @param  string  $key
     * @return string
     */
    protected static function wildcards($key)
    {
        $patterns = [
            '/\/{(.*)\:(.*)\?}/' => '/(?:/(${2})',
            '/\/{(.*)\:(.*)}/' => '/(${2})',
            '/\/{(.*)\?}/' => '/(:all?)',
            '/\/{(.*)}/' => '/(:all)',
        ];

        preg_match_all('/({[^}]+})/', $key, $matches);

        foreach (array_get($matches, '0', []) as $match) {
            foreach ($patterns as $search => $replace) {
                $key = str_replace('/' . $match, preg_replace($search, $replace, '/' . $match, 1), $key);
            }
        }

        list($search, $replace) = array_divide(static::$optional);

        // For optional parameters, first translate the wildcards to their
        // regex equivalent, sans the ")?" ending. We'll add the endings
        // back on when we know the replacement count.
        $key = str_replace($search, $replace, $key);

        if ($count = count(explode('?', $key)) - 1) {
            $key .= str_repeat(')?', $count);
        }

        $key = strtr($key, static::$patterns);

        $key = str_replace('/(?', '(?', $key);

        return $key;
    }

    /**
     * Get all of the registered routes, with fallbacks at the end.
     *
     * @return array
     */
    public static function routes()
    {
        $routes = static::$routes;

        foreach (static::$methods as $method) {
            // It's possible that the routes array may not contain any routes for the
            // method, so we'll seed each request method with an empty array if it
            // doesn't already contain any routes.
            if (!isset($routes[$method])) $routes[$method] = array();

            $fallback = array_get(static::$fallback, $method, array());

            // When building the array of routes, we'll merge in all of the fallback
            // routes for each request method individually. This allows us to avoid
            // collisions when merging the arrays together.
            $routes[$method] = array_merge($routes[$method], $fallback);
        }

        return $routes;
    }

    /**
     * Grab all of the routes for a given request method.
     *
     * @param  string  $method
     * @return array
     */
    public static function method($method)
    {
        $routes = array_get(static::$routes, $method, array());

        return array_merge($routes, array_get(static::$fallback, $method, array()));
    }

    /**
     * Get all of the wildcard patterns
     *
     * @return array
     */
    public static function patterns()
    {
        return array_merge(static::$patterns, static::$optional);
    }

    /**
     * Get a string repeating a URI pattern any number of times.
     *
     * @param  string  $pattern
     * @param  int     $times
     * @return string
     */
    protected static function repeat($pattern, $times)
    {
        return implode('/', array_fill(0, $times, $pattern));
    }
}
