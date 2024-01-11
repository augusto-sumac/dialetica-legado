<?php

class RoutingFilterCollection
{

    /**
     * The filters contained by the collection.
     *
     * @var string|array
     */
    public $filters = array();

    /**
     * The parameters specified for the filter.
     *
     * @var mixed
     */
    public $parameters;

    /**
     * The included controller methods.
     *
     * @var array
     */
    public $only = array();

    /**
     * The excluded controller methods.
     *
     * @var array
     */
    public $except = array();

    /**
     * The HTTP methods for which the filter applies.
     *
     * @var array
     */
    public $methods = array();

    /**
     * Create a new filter collection instance.
     *
     * @param  string|array  $filters
     * @param  mixed         $parameters
     * @return void
     */
    public function __construct($filters, $parameters = null)
    {
        $this->parameters = $parameters;
        $this->filters = RoutingFilter::parse($filters);
    }

    /**
     * Parse the filter string, returning the filter name and parameters.
     *
     * @param  string  $filter
     * @return array
     */
    public function get($filter)
    {
        // If the parameters were specified by passing an array into the collection,
        // then we will simply return those parameters. Combining passed parameters
        // with parameters specified directly in the filter attachment is not
        // currently supported by the framework.
        if (!is_null($this->parameters)) {
            return array($filter, $this->parameters());
        }

        // If no parameters were specified when the collection was created or
        // in the filter string, we will just return the filter name as is
        // and give back an empty array of parameters.
        return array($filter, array());
    }

    /**
     * Evaluate the collection's parameters and return a parameters array.
     *
     * @return array
     */
    protected function parameters()
    {
        if ($this->parameters instanceof Closure) {
            $this->parameters = call_user_func($this->parameters);
        }

        return $this->parameters;
    }

    /**
     * Get all request headers
     *
     * @return array The request headers
     */
    public static function getRequestHeaders()
    {
        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // Method getallheaders() not available: manually extract 'm
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account
     *
     * @return string The Request method to handle
     */
    public static function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = static::getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * Determine if this collection's filters apply to a given method.
     *
     * @param  string  $method
     * @return bool
     */
    public function applies($method)
    {
        if (count($this->only) > 0 and !in_array($method, $this->only)) {
            return false;
        }

        if (count($this->except) > 0 and in_array($method, $this->except)) {
            return false;
        }

        $request = strtolower(static::getRequestMethod());

        if (count($this->methods) > 0 and !in_array($request, $this->methods)) {
            return false;
        }

        return true;
    }

    /**
     * Set the excluded controller methods.
     *
     * <code>
     *		// Specify a filter for all methods except "index"
     *		$this->filter('before', 'auth')->except('index');
     *
     *		// Specify a filter for all methods except "index" and "home"
     *		$this->filter('before', 'auth')->except(array('index', 'home'));
     * </code>
     *
     * @param  array              $methods
     * @return RoutingFilterCollection
     */
    public function except($methods)
    {
        $this->except = (array) $methods;
        return $this;
    }

    /**
     * Set the included controller methods.
     *
     * <code>
     *		// Specify a filter for only the "index" method
     *		$this->filter('before', 'auth')->only('index');
     *
     *		// Specify a filter for only the "index" and "home" methods
     *		$this->filter('before', 'auth')->only(array('index', 'home'));
     * </code>
     *
     * @param  array              $methods
     * @return RoutingFilterCollection
     */
    public function only($methods)
    {
        $this->only = (array) $methods;
        return $this;
    }

    /**
     * Set the HTTP methods for which the filter applies.
     *
     * <code>
     *		// Specify that a filter only applies on POST requests
     *		$this->filter('before', 'csrf')->on('post');
     *
     *		// Specify that a filter applies for multiple HTTP request methods
     *		$this->filter('before', 'csrf')->on(array('post', 'put'));
     * </code>
     *
     * @param  array              $methods
     * @return RoutingFilterCollection
     */
    public function on($methods)
    {
        $this->methods = array_map('strtolower', (array) $methods);
        return $this;
    }
}
