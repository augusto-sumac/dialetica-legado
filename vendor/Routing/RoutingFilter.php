<?php

require_once(__DIR__ . '/RoutingFilterCollection.php');

class RoutingFilter
{

    /**
     * The route filters for the application.
     *
     * @var array
     */
    public static $filters = array();

    /**
     * The route filters that are based on a pattern.
     *
     * @var array
     */
    public static $patterns = array();

    /**
     * All of the registered filter aliases.
     *
     * @var array
     */
    public static $aliases = array();

    /**
     * Register a filter for the application.
     *
     * <code>
     *		// Register a closure as a filter
     *		RoutingFilter::register('before', function() {});
     *
     *		// Register a class callback as a filter
     *		RoutingFilter::register('before', array('Class', 'method'));
     * </code>
     *
     * @param  string  $name
     * @param  mixed   $callback
     * @return void
     */
    public static function register($name, $callback)
    {
        if (isset(static::$aliases[$name])) $name = static::$aliases[$name];

        // If the filter starts with "pattern: ", the filter is being setup to match on
        // all requests that match a given pattern. This is nice for defining filters
        // that handle all URIs beginning with "admin" for example.
        if (starts_with($name, 'pattern: ')) {
            foreach (explode(', ', substr($name, 9)) as $pattern) {
                static::$patterns[$pattern] = $callback;
            }
        } else {
            static::$filters[$name] = $callback;
        }
    }

    /**
     * Alias a filter so it can be used by another name.
     *
     * This is convenient for shortening filters that are registered by bundles.
     *
     * @param  string  $filter
     * @param  string  $alias
     * @return void
     */
    public static function alias($filter, $alias)
    {
        static::$aliases[$alias] = $filter;
    }

    /**
     * Parse a filter definition into an array of filters.
     *
     * @param  string|array  $filters
     * @return array
     */
    public static function parse($filters)
    {
        if (is_string($filters)) {
            $filters   = preg_replace('/\s|\s+/', '', $filters);
            $separator = strpos($filters, ',') !== false ? ',' : '|';
            $filters   = explode($separator, $filters);
        }

        return (array) $filters;
    }

    /**
     * Call a filter or set of filters.
     *
     * @param  array   $collections
     * @param  array   $pass
     * @param  bool    $override
     * @return mixed
     */
    public static function run($collections, $pass = array(), $override = false)
    {
        foreach ($collections as $collection) {
            foreach ($collection->filters as $filter) {
                list($filter, $parameters) = $collection->get($filter);

                if (!isset(static::$filters[$filter])) continue;

                $callback = static::$filters[$filter];

                // Parameters may be passed into filters by specifying the list of parameters
                // as an array, or by registering a Closure which will return the array of
                // parameters. If parameters are present, we will merge them with the
                // parameters that were given to the method.
                $response = call_user_func_array($callback, array_merge($pass, $parameters));

                // "Before" filters may override the request cycle. For example, an auth
                // filter may redirect a user to a login view if they are not logged in.
                // Because of this, we will return the first filter response if
                // overriding is enabled for the filter collections
                if (!is_null($response) and $override) {
                    return $response;
                }
            }
        }
    }
}
