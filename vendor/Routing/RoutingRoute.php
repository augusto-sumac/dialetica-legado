<?php

require_once(__DIR__ . '/RoutingFilter.php');
require_once(__DIR__ . '/RoutingRouter.php');

class RoutingRoute
{

    /**
     * The URI the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The request method the route responds to.
     *
     * @var string
     */
    public $method;

    /**
     * The name of the controller used by the route.
     *
     * @var string
     */
    public $controller;

    /**
     * The name of the controller action used by the route.
     *
     * @var string
     */
    public $controller_action;

    /**
     * The action that is assigned to the route.
     *
     * @var mixed
     */
    public $action;

    /**
     * The parameters that will be passed to the route callback.
     *
     * @var array
     */
    public $parameters;

    /**
     * Create a new RoutingRoute instance.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $action
     * @param  array   $parameters
     */
    public function __construct($method, $uri, $action, $parameters = array())
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->action = $action;

        // We'll set the parameters based on the number of parameters passed
        // compared to the parameters that were needed. If more parameters
        // are needed, we'll merge in the defaults.
        $this->parameters($action, $parameters);
    }

    /**
     * Set the parameters array to the correct value.
     *
     * @param  array   $action
     * @param  array   $parameters
     * @return void
     */
    protected function parameters($action, $parameters)
    {
        $defaults = (array) array_get($action, 'defaults');

        // If there are less parameters than wildcards, we will figure out how
        // many parameters we need to inject from the array of defaults and
        // merge them into the main array for the route.
        if (count($defaults) > count($parameters)) {
            $defaults = array_slice($defaults, count($parameters));

            $parameters = array_merge($parameters, $defaults);
        }

        $this->parameters = $parameters;
    }

    /**
     * Call a given route and return the route's response.
     *
     * @return Response
     */
    public function call()
    {
        // The route is responsible for running the global filters, and any
        // filters defined on the route itself, since all incoming requests
        // come through a route (either defined or ad-hoc).
        $response = RoutingFilter::run($this->filters('before'), array(), true);


        if (is_null($response)) {
            $response = $this->response();
        }

        RoutingFilter::run($this->filters('after'), array(&$response));

        return $response;
    }

    /**
     * Execute the route action and return the response.
     *
     * Unlike the "call" method, none of the attached filters will be run.
     *
     * @return mixed
     */
    public function response()
    {
        // If the route does not have a delegate, then it must be a Closure
        // instance or have a Closure in its action array, so we will try
        // to locate the Closure and call it directly.
        $handler = $this->handler();

        if (!is_null($handler)) {
            return call_user_func_array($handler, $this->parameters);
        }
    }

    /**
     * Get the filters that are attached to the route for a given event.
     *
     * @param  string  $event
     * @return array
     */
    protected function filters($event)
    {
        $filters = array_unique(array($event));

        // Next we will check to see if there are any filters attached to
        // the route for the given event. If there are, we'll merge them
        // in with the global filters for the event.
        if (isset($this->action[$event])) {
            $assigned = RoutingFilter::parse($this->action[$event]);

            $filters = array_merge($filters, $assigned);
        }

        // Next we will attach any pattern type filters to the array of
        // filters as these are matched to the route by the route's
        // URI and not explicitly attached to routes.
        if ($event === 'before') {
            $filters = array_merge($filters, $this->patterns());
        }

        return array(new RoutingFilterCollection($filters));
    }

    /**
     * Get the pattern filters for the route.
     *
     * @return array
     */
    protected function patterns()
    {
        $filters = array();

        // We will simply iterate through the registered patterns and
        // check the URI pattern against the URI for the route and
        // if they match we'll attach the filter.
        foreach (RoutingFilter::$patterns as $pattern => $filter) {
            if (Str::is($pattern, $this->uri)) {
                // If the filter provided is an array then we need to register
                // the filter before we can assign it to the route.
                if (is_array($filter)) {
                    list($filter, $callback) = array_values($filter);

                    RoutingFilter::register($filter, $callback);
                }

                $filters[] = $filter;
            }
        }

        return (array) $filters;
    }

    /**
     * Get the anonymous function assigned to handle the route.
     *
     * @return Closure
     */
    protected function handler()
    {
        return array_first($this->action, function ($key, $value) {
            return $value instanceof Closure;
        });
    }

    /**
     * Determine if the route has a given name.
     *
     * <code>
     *		// Determine if the route is the "login" route
     *		$login = Request::route()->is('login');
     * </code>
     *
     * @param  string  $name
     * @return bool
     */
    public function is($name)
    {
        return array_get($this->action, 'as') === $name;
    }

    /**
     * Register a GET route with the router.
     *
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function get($route, $action)
    {
        RoutingRouter::register('GET', $route, $action);
    }

    /**
     * Register a POST route with the router.
     *
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function post($route, $action)
    {
        RoutingRouter::register('POST', $route, $action);
    }

    /**
     * Register a PUT route with the router.
     *
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function put($route, $action)
    {
        RoutingRouter::register('PUT', $route, $action);
    }

    /**
     * Register a DELETE route with the router.
     *
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function delete($route, $action)
    {
        RoutingRouter::register('DELETE', $route, $action);
    }

    /**
     * Register a route that handles any request method.
     *
     * @param  string|array  $route
     * @param  mixed         $action
     * @return void
     */
    public static function any($route, $action)
    {
        RoutingRouter::register('*', $route, $action);
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
        RoutingRouter::group($attributes, $callback);
    }

    /**
     * Register many request URIs to a single action.
     *
     * @param  array  $routes
     * @param  mixed  $action
     * @return void
     */
    public static function share($routes, $action)
    {
        RoutingRouter::share($routes, $action);
    }

    /**
     * Register a route filter.
     *
     * @param  string  $name
     * @param  mixed   $callback
     * @return void
     */
    public static function filter($name, $callback)
    {
        RoutingFilter::register($name, $callback);
    }

    /**
     * Calls the specified route and returns its response.
     *
     * @param  string    $method
     * @param  string    $uri
     * @return Response
     */
    public static function forward($method, $uri)
    {
        return RoutingRouter::route(strtoupper($method), $uri)->call();
    }
}
