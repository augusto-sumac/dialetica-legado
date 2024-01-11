<?php

function mask($val, $mask)
{
    $val = trim($val);

    // if ($mask === 'cpf_cnpj') {
    //     $val = only_numbers($val);
    //     $mask = strlen($val) > 11 ? '##.###.###/####-##' : '###.###.###-##';
    //     $val = $val ? str_pad($val, strlen($val) <= 11 ? 11 : 14, '0', STR_PAD_LEFT) : null;
    // }

    // if ($mask === 'phone') {
    //     $val = only_numbers($val);
    //     $mask = strlen($val) > 10 ? '(##) #####-####' : '(##) ####-####';
    // }

    // if ($mask === 'cep') {
    //     $val = only_numbers($val);
    //     $mask = '#####-###';
    //     if (strlen($val) < 8) {
    //         $val = null;
    //     }
    // }

    if (in_array($mask, ['cpf_cnpj', 'phone', 'cep'])) {
        return only_numbers($val);
    }

    if (strlen($val) === 0) {
        return null;
    }

    $masked = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k]))
                $masked .= $val[$k++];
        } else {
            if (isset($mask[$i]))
                $masked .= $mask[$i];
        }
    }
    return $masked;
}

function only_numbers($str)
{
    return $str ? preg_replace('/\D/', '', $str) : null;
}

/**
 * Money Format
 * @param  double $value
 * @return string
 */
function toMoney($value)
{
    return number_format($value, 2, ',', '.');
}

/**
 * From BRL string to double
 *
 * @param [type] $value
 * @return void
 */
function toNumber($value)
{
    if (preg_match('/\..*\,/i', $value)) {
        return (float) str_replace(',', '.', str_replace('.', '', $value));
    }

    if (preg_match('/\,.*\./i', $value)) {
        return (float) str_replace(',', '', $value);
    }

    if (preg_match('/\,/i', $value)) {
        return (float) str_replace(',', '.', $value);
    }

    return (float) $value;
}

/**
 * Converte DateTime String From MySql To Date Format
 * 
 * @param  string $datetime
 * @return string
 */
function datetime_to_mysql($datetime)
{
    return datetimeToMySql($datetime);
}

/**
 * Converte DateTime String to MySql Format
 * 
 * @param  string $datetime
 * @return string
 */
function datetime_from_mysql($datetime)
{
    return datetimeFromMySql($datetime);
}

function datetimeToMySql($datetime)
{
    return parseDateTime($datetime, '/', '-');
}

function datetimeFromMySql($datetime)
{
    return parseDateTime($datetime, '-', '/');
}

function parseDateTime($datetime, $explode, $implode)
{
    $datetime = explode(' ', $datetime);
    $date = array_get($datetime, 0);
    $time = array_get($datetime, 1);
    $date = implode($implode, array_reverse(explode($explode, $date)));

    if ($time) {
        return $date . ' ' . $time;
    }

    return strlen($date) > 3 ? $date : null;
}

/**
 * Retorna se a requisição atual é AJAX
 * @return boolean
 */
function isAjax()
{
    return (mb_strtolower(array_get($_SERVER, 'HTTP_X_REQUESTED_WITH', '__')) === 'xmlhttprequest');
}

interface Responsable
{
    public function getResponse();
}

class GenericResponseException extends \Exception implements Responsable
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}

class ValidationException extends \Exception implements Responsable
{
    public $validation;

    public $response;

    public function __construct(Validator $validation)
    {
        $this->validation = $validation;

        $this->response = response_json([
            'message' => 'Existem campos inválidos',
            'errors' => $validation->errors->messages
        ], 422);
    }

    public function getResponse()
    {
        return $this->response;
    }
}

function validate($rules = array(), $data = [], $messages = [])
{
    if (empty($data)) {
        $data = input();
    }

    foreach ($data as $key => $value) {
        switch ($key) {
            case 'quantidade':
            case 'quantidade_informada':
            case 'quantidade_recebida':
                $value = str_replace(',', '.', str_replace('.', '', $value));
                break;

            case 'cpf':
            case 'cnpj':
            case 'cpf_cnpj':
                $value = only_numbers($value);
                break;

            case 'data':
            case 'data_emissao':
                $value = datetimeToMySql($value);
                break;
        }
        $data[$key] = $value;
    }

    $validation = Validator::make($data, $rules, $messages);

    if ($validation->fails()) {
        throw new ValidationException($validation);
    }

    return $validation;
}

function render_page($view)
{
    $view = str_replace('pages.', '', $view);

    if (func_num_args() === 1) {
        return function () use ($view) {
            return view('pages.' . $view, ['id' => null]);
        };
    }

    $args = func_get_args();

    return view('pages.' . $view, $args[1]);
}

/**
 * Simples VIEW loader
 *
 * $content = view('view/name');
 * 
 * @param  string  $view
 * @param  array   $data
 * @param  boolean $blade 
 * @param  boolean $ntf
 * @return string
 */
function view($view, $data = array())
{
    return Blade::render($view, $data, true);
}

/**
 * GEt a view path
 * @param  string  $view
 * @param  boolean $blade
 * @return string
 */
function viewPath($view = '')
{
    if (file_exists($view)) {
        return $view;
    }

    $view = str_replace(array(VIEW_PATH, '.php', '.blade.php'), '', $view);
    $view = rtrim(ltrim(str_replace('.', DS, $view), DS), DS);

    $path = VIEW_PATH . $view . '.blade.php';
    if (file_exists($path)) {
        return $path;
    }

    $path = VIEW_PATH . $view . '.php';
    if (file_exists($path)) {
        return $path;
    }

    return false;
}

/**
 * Get the current HTTP path info.
 *
 * @return string
 */
function uri_path()
{
    /** @psalm-var array<string, string> $_SERVER */
    $script_name = array_get($_SERVER, 'SCRIPT_NAME', '');
    $request_uri = array_get($_SERVER, 'REQUEST_URI', '');

    // NOTE: When using built-in server with a router script, SCRIPT_NAME will be same as the REQUEST_URI
    if (PHP_SAPI === 'cli-server') {
        $script_name = '';
    }

    $query_string = strpos($request_uri, '?');
    $request_uri = $query_string === false ? $request_uri : substr($request_uri, 0, $query_string);
    $request_uri = rawurldecode($request_uri);
    $script_path = str_replace('\\', '/', \dirname($script_name));

    if (str_replace('/', '', $script_path) === '') {
        return '/' . ltrim($request_uri, '/');
    }

    return '/' . ltrim(preg_replace("#^$script_path#", '', $request_uri, 1), '/');
}

/**
 * Get the absolute project's URI.
 *
 * @param string|null $protocol
 * @return string
 */
function uri(string $protocol = null, $path = null)
{
    if ($protocol === null) {
        $https = array_get($_SERVER, 'HTTPS', array_get($_SERVER, 'HTTP_SEC_FETCH_DEST', ''));

        if (preg_match('/localhost\:[0-9]+/', array_get($_SERVER, 'HTTP_HOST', 'nothing'))) {
            $https = null;
        }

        /**
         * @var array<string, string> $_SERVER
         * @var string $https
         */
        $protocol = empty($https) || env('DEV_MODE', false) ? 'http' : 'https';
    }

    if (env('APP_DO')) {
        $protocol = 'https';
    }

    /** @var string $http_host */
    $http_host = array_get($_SERVER, 'HTTP_HOST', '');

    if (!$path) {
        $path = uri_path();
    }

    return $protocol . '://' . $http_host . $path;
}

/**
 * Returns a path based on the projects base url.
 *
 * @param string|null $path
 * @return string
 */
function url(string $path = null): string
{
    if ($path === null) {
        $path = '/';
    }

    if (preg_match('/http(s)?:\//i', $path)) {
        return $path;
    }

    return uri(null, '/' . ltrim($path, '/'));
}


/**
 * Url Atual
 *
 * $current = urlCurrent();
 * 
 * @return string
 */
function urlCurrent()
{
    return uri();
}

function base_url()
{
    return uri();
}

function isCurrentUrl($url = '', $exact = false)
{
    $url = url($url);
    $current = urlCurrent();

    if ($exact) {
        return $url === $current;
    }

    return strpos($current, $url) !== false;
}

function addActiveClass($url = '', $exact = false)
{
    return isCurrentUrl($url, $exact) ? 'active' : '';
}

/**
 * Forca o redirecionamento para a url
 * 
 * @param  string $url
 * @return mixed
 */
function redirect($url = '/')
{
    response_redirect($url);
    return '';
}

function routes()
{
    $routes = [];
    foreach (Router::routes() as $method => $collection) {
        foreach ($collection as $path => $parameters) {
            $before = array_get($parameters, 'before', []);
            $routes[$path] = compact('path', 'method', 'before');
        }
    }

    ksort($routes);

    return array_values($routes);
}

function apply_sql_bindings($sql, $bindings)
{
    $bindings = array_values($bindings);

    // White the bindings
    foreach ($bindings as $binding) {
        $binding = DB::escape($binding);

        $sql = preg_replace('/\?/', $binding, $sql, 1);
    }

    // Removing extra white spaces
    $sql = str_replace(array("\r\n", "\n", "\r",), '', $sql);

    return $sql;
}

/**
 * Pega a ultima instrução SQL executada
 *
 * DB::table('x')->get();
 *
 * $sql = db_last_query(); -> 'SELECT * FROM 'x''
 * 
 * @return string
 */
function db_last_query()
{
    $last_query = DB::last_query();

    if (!$last_query) {
        return null;
    }

    if (is_string($last_query)) {
        return $last_query;
    }

    $last_query = array_values($last_query);

    list($sql, $bindings, $time) = $last_query;

    return apply_sql_bindings($sql, $bindings);
}


/**
 * Convert object to array
 * @param  object $o
 * @return array|boolean
 */
function objectToArray($d)
{
    if (is_object($d)) {
        $d = get_object_vars($d);
    }

    return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
}

/**
 * Convert array to object
 * @param  array $a
 * @return object|boolean
 */
function arrayToObject($d)
{
    return is_array($d) ? (object) array_map(__FUNCTION__, $d) : $d;
}

function secure_json_decode($mixed, $has_array = true)
{
    if (is_object($mixed) || is_array($mixed)) {
        return $has_array ? objectToArray($mixed) : $mixed;
    }

    try {
        $mixed = json_decode($mixed);

        if (json_last_error() === JSON_ERROR_NONE) {
            return secure_json_decode($mixed);
        }
    } catch (\Exception $e) {
        // ...
    }

    return [];
}

/**
 * Captura um valor enviado na requisição
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function input($key = null, $default = null)
{
    if (!isset($GLOBALS['APP_INPUT_DATA'])) {
        $input = array_merge($_GET, $_POST);

        if ($json = file_get_contents('php://input')) {
            $json = (array)json_decode($json);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = array_merge($input, $json);
            }
        }

        $GLOBALS['APP_INPUT_DATA'] = $input;
    }

    $input = array_get($GLOBALS, 'APP_INPUT_DATA', array());

    return $key ? array_get($input, $key, $default) : $input;
}

/**
 * Gera um astring chave="valor" de atributos 
 * para tags HTML
 *
 * $attrs = attributes(array('id' => 'id', 'class' => 'class another-class')); -> 'id="id" class="class another-clas"'
 * 
 * @param  array $attributes
 * @return string
 */
function attributes($attributes)
{
    $html = array();

    foreach ((array) $attributes as $key => $value) {
        // For numeric keys, we will assume that the key and the value are the
        // same, as this will convert HTML attributes such as "required" that
        // may be specified as required="required", etc.
        if (is_numeric($key)) $key = $value;

        if (!is_null($value)) {
            $html[] = $key . '="' . $value . '"';
        }
    }

    return (count($html) > 0) ? ' ' . implode(' ', $html) : '';
}

/**
 * Gera uma tage A com os atributos passados
 *
 * $link = a('/url/to/route', 'Title', array('id' => 'id', 'class' => 'a-class another-class')); -> '<a href="..." id="..." class="..." title="Title">Title</a>'
 * 
 * @param  string $url
 * @param  string $title
 * @param  array  $attributes
 * @return string
 */
function a($url, $title = null, $attributes = array())
{
    if (is_null($title)) $title = $url;

    $attributes['title'] = $title;
    $attributes['href']  = url($url);

    return '<a' . attributes($attributes) . '>' . $title . '</a>';
}

/**
 * BootStrap Style Flash Messages
 * 
 * @return string
 */
function showFlashAlerts()
{
    $button = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

    $html = '';
    foreach (array('success', 'info', 'warning', 'error') as $type) {
        foreach (array_get($_SESSION, $type, array()) as $message) {
            $css_type = $type == 'error' ? 'danger' : $type;

            $html .= '<div class="alert alert-' . $css_type . ' alert-top alert-top-' . $css_type . ' alert-dismissible fade show" role="alert"> 
                ' . $button . $message . '
            </div>';
        }
        $_SESSION[$type] = array();
    }

    return $html;
}

/**
 * BootStrap Alert Messages
 * 
 * @param string $type
 * @param string $message
 */
function Alert($type = 'info', $message = null)
{
    if (!isset($_SESSION[$type])) {
        $_SESSION[$type] = array();
    }
    $_SESSION[$type][] = $message;
}

/**
 * Alias To Alert Function
 * 
 * @param string $message
 */
function AlertSuccess($message = null)
{
    Alert('success', $message);
}

/**
 * Alias To Alert Function
 * 
 * @param string $message
 */
function AlertInfo($message = null)
{
    Alert('info', $message);
}

/**
 * Alias To Alert Function
 * 
 * @param string $message
 */
function AlertWarning($message = null)
{
    Alert('warning', $message);
}

/**
 * Alias To Alert Function
 * 
 * @param string $message
 */
function AlertError($message = null)
{
    Alert('error', $message);
}

function is_cli()
{
    return php_sapi_name() === "cli";
}

/**
 * Return a Pre Formatted string
 * 
 * @param  mixed $m
 * @return string
 */
function pr($m, $return = true)
{
    if (is_cli()) {
        echo print_r($m, true) . "\n";
        return;
    }

    $pre = '<pre>' . print_r($m, true) . '</pre>';

    if ($return) {
        return $pre;
    }

    echo $pre;
}

/**
 * Gera um string pronta para ser utilizado como url
 * 
 * @param   string
 * @param   string
 * @return  string
 */
if (!function_exists('sanitize_url')) {
    function sanitize_url($string = null, $separator = '-')
    {
        if (function_exists('sanitize_filename')) {
            $string = sanitize_filename($string);
        }
        $string = preg_replace("`\[.*\]`U", "", $string);
        $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '_', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string);
        $string = preg_replace(array("`[^a-z0-9\.-]`i", "`[-]+`"), $separator, $string);
        return strtolower(trim($string, '-'));
    }
}

/**
 * Codeigniter sanitize_filename
 *
 * @param   string
 * @param   bool
 * @return  string
 */
if (!function_exists('sanitize_filename')) {
    function sanitize_filename($str, $relative_path = FALSE)
    {
        $bad = array("../", "<!--", "-->", "<", ">", "'", '"', '&', '$', '#', '{', '}', '[', ']', '=', ';', '?', "%20", "%22", "%3c", "%253c", "%3e", "%0e", "%28", "%29", "%2528", "%26", "%24", "%3f", "%3b", "%3d",);
        if (!$relative_path) {
            $bad[] = './';
            $bad[] = '/';
        }
        $str = remove_invisible_characters($str, FALSE);
        return stripslashes(str_replace($bad, '', $str));
    }
}

/**
 * CodeIgniter Remove Invisible Characters
 *
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 *
 * @access  public
 * @param   string
 * @return  string
 */
if (!function_exists('remove_invisible_characters')) {
    function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $no_print = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $no_print[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $no_print[] = '/%1[0-9a-f]/';   // url encoded 16-31
        }

        $no_print[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($no_print, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

/**
 * Retona o tamanho do arquivo formatado
 *
 * $filesize = getFileSize(32000000); -> 32MB
 * 
 * @param  integer $bytes
 * @param  integer $decimals
 * @return string
 */
function getFileSize($bytes, $decimals = 2)
{
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
}

// Laravel Based Helpers
/**
 * Get an item from an array using "dot" notation.
 *
 * <code>
 *      // Get the $array['user']['name'] value from the array
 *      $name = array_get($array, 'user.name');
 *
 *      // Return a default from if the specified item doesn't exist
 *      $name = array_get($array, 'user.name', 'Taylor');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) return $array;

    // To retrieve the array item using dot syntax, we'll iterate through
    // each segment in the key and look for that value. If it exists, we
    // will return it, otherwise we will set the depth of the array and
    // look for the next segment.
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) or !array_key_exists($segment, $array)) {
            return value($default);
        }

        $array = $array[$segment];
    }

    return $array;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * <code>
 *      // Set the $array['user']['name'] value on the array
 *      array_set($array, 'user.name', 'Taylor');
 *
 *      // Set the $array['user']['name']['first'] value on the array
 *      array_set($array, 'user.name.first', 'Michael');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return void
 */
function array_set(&$array, $key, $value)
{
    if (is_null($key)) return $array = $value;

    $keys = explode('.', $key);

    // This loop allows us to dig down into the array to a dynamic depth by
    // setting the array value for each level that we dig into. Once there
    // is one key left, we can fall out of the loop and set the value as
    // we should be at the proper depth.
    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an
        // empty array to hold the next value, allowing us to create the
        // arrays to hold the final value.
        if (!isset($array[$key]) or !is_array($array[$key])) {
            $array[$key] = array();
        }

        $array = &$array[$key];
    }

    $array[array_shift($keys)] = $value;
}

/**
 * Remove an array item from a given array using "dot" notation.
 *
 * <code>
 *      // Remove the $array['user']['name'] item from the array
 *      array_forget($array, 'user.name');
 *
 *      // Remove the $array['user']['name']['first'] item from the array
 *      array_forget($array, 'user.name.first');
 * </code>
 *
 * @param  array   $array
 * @param  string  $key
 * @return void
 */
function array_forget(&$array, $key)
{
    $keys = explode('.', $key);

    // This loop functions very similarly to the loop in the "set" method.
    // We will iterate over the keys, setting the array value to the new
    // depth at each iteration. Once there is only one key left, we will
    // be at the proper depth in the array.
    while (count($keys) > 1) {
        $key = array_shift($keys);

        // Since this method is supposed to remove a value from the array,
        // if a value higher up in the chain doesn't exist, there is no
        // need to keep digging into the array, since it is impossible
        // for the final value to even exist.
        if (!isset($array[$key]) or !is_array($array[$key])) {
            return;
        }

        $array = &$array[$key];
    }

    unset($array[array_shift($keys)]);
}

/**
 * Return the first element in an array which passes a given truth test.
 *
 * <code>
 *      // Return the first array element that equals "Taylor"
 *      $value = array_first($array, function($k, $v) {return $v == 'Taylor';});
 *
 *      // Return a default value if no matching element is found
 *      $value = array_first($array, function($k, $v) {return $v == 'Taylor'}, 'Default');
 * </code>
 *
 * @param  array    $array
 * @param  Closure  $callback
 * @param  mixed    $default
 * @return mixed
 */
function array_first($array, $callback, $default = null)
{
    foreach ($array as $key => $value) {
        if (call_user_func($callback, $key, $value)) return $value;
    }

    return value($default);
}

/**
 * Recursively remove slashes from array keys and values.
 *
 * @param  array  $array
 * @return array
 */
function array_strip_slashes($array)
{
    $result = array();

    foreach ($array as $key => $value) {
        $key = stripslashes($key);

        // If the value is an array, we will just recurse back into the
        // function to keep stripping the slashes out of the array,
        // otherwise we will set the stripped value.
        if (is_array($value)) {
            $result[$key] = array_strip_slashes($value);
        } else {
            $result[$key] = stripslashes($value);
        }
    }

    return $result;
}

/**
 * Divide an array into two arrays. One with keys and the other with values.
 *
 * @param  array  $array
 * @return array
 */
function array_divide($array)
{
    return array(array_keys($array), array_values($array));
}

/**
 * Pluck an array of values from an array.
 *
 * @param  array   $array
 * @param  string  $key
 * @return array
 */
function array_pluck($array, $key)
{
    /*return array_map(function($v) use ($key)
        {
            return is_object($v) ? $v->$key : $v[$key];

        }, $array, $array, $key);*/
    foreach ($array as $v) {
        return is_object($v) ? $v->$key : $v[$key];
    }
}

/**
 * Get a subset of the items from the given array.
 *
 * @param  array  $array
 * @param  array  $keys
 * @return array
 */
function array_only($array, $keys)
{
    return array_intersect_key($array, array_flip((array) $keys));
}

/**
 * Get all of the given array except for a specified array of items.
 *
 * @param  array  $array
 * @param  array  $keys
 * @return array
 */
function array_except($array, $keys)
{
    return array_diff_key($array, array_flip((array) $keys));
}

/**
 * Return the first element of an array.
 *
 * This is simply a convenient wrapper around the "reset" method.
 *
 * @param  array  $array
 * @return mixed
 */
function head($array)
{
    return reset($array);
}

/**
 * Determine if a given string begins with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string ends with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function ends_with($haystack, $needle)
{
    return $needle == substr($haystack, strlen($haystack) - strlen($needle));
}

/**
 * Determine if a given string contains a given sub-string.
 *
 * @param  string        $haystack
 * @param  string|array  $needle
 * @return bool
 */
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        foreach ((array) $needle as $n) {
            if (strpos($haystack, $n) !== false) return true;
        }

        return false;
    }
}

/**
 * Cap a string with a single instance of the given string.
 *
 * @param  string  $value
 * @param  string  $cap
 * @return string
 */
function str_finish($value, $cap)
{
    return rtrim($value, $cap) . $cap;
}

/**
 * Determine if the given object has a toString method.
 *
 * @param  object  $value
 * @return bool
 */
function str_object($value)
{
    return is_object($value) and method_exists($value, '__toString');
}

/**
 * Get the "class basename" of a class or object.
 *
 * The basename is considered to be the name of the class minus all namespaces.
 *
 * @param  object|string  $class
 * @return string
 */
function class_basename($class)
{
    if (is_object($class)) $class = get_class($class);

    return basename(str_replace('\\', '/', $class));
}

/**
 * Return the value of the given item.
 *
 * If the given item is a Closure the result of the Closure will be returned.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
    return (is_callable($value) and !is_string($value)) ? call_user_func($value) : $value;
}

/**
 * Short-cut for constructor method chaining.
 *
 * @param  mixed  $object
 * @return mixed
 */
function with($object)
{
    return $object;
}

/**
 * Determine if the current version of PHP is at least the supplied version.
 *
 * @param  string  $version
 * @return bool
 */
function has_php($version)
{
    return version_compare(PHP_VERSION, $version) >= 0;
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
            default:
        }

        if (strlen($value) > 1 && starts_with($value, '"') && ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
