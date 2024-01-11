<?php

declare(strict_types=1);

/**
 * Outputs the given parameters based on a HTTP response.
 *
 * @param string $content The HTTP response body
 * @param int $code The HTTP response code code
 * @param string $mimeType A value for HTTP Header Content-Type
 * @param string $charset The HTTP response charset
 *
 * @return int Returns 1, always
 */
function response_output(string $content = '', int $code = 204, array $headers = [], string $mimeType = 'text/plain', string $charset = 'utf-8')
{
    http_response_code($code);
    \header(sprintf('Content-Type: %s;charset=%s', $mimeType, $charset));
    response_headers($headers);

    return $content;
}

/**
 * Outputs a HTTP response as simple text.
 *
 * @param string $content The HTTP response body
 * @param int $code The HTTP response status code
 * @param string $charset The HTTP response charset
 *
 * @return int Returns 1, always
 */
function response_text(string $content, int $code = 200, array $headers = [], string $charset = 'utf-8')
{
    return response_output(strval($content), $code, $headers, 'text/plain', $charset);
}

/**
 * Outputs a HTML HTTP response.
 *
 * @param string $content The HTTP response body
 * @param int $code The HTTP response status code
 * @param string $charset The HTTP response charset
 *
 * @return int Returns 1, always
 */
function response_html(string $content, int $code = 200, array $headers = [], string $charset = 'utf-8')
{
    return response_output($content, $code, $headers, 'text/html', $charset);
}

/**
 * Outputs the given content as JSON mime type.
 *
 * @param string $content The HTTP response body
 * @param int $code The HTTP response status code
 * @param string $charset The HTTP response charset
 *
 * @return int Returns 1, always
 */
function response_json_str(string $content, int $code = 200, array $headers = [], string $charset = 'utf-8')
{
    return response_output(strval($content), $code, $headers, 'application/json', $charset);
}

/**
 * Outputs the given content encoded as JSON string.
 *
 * @param mixed $content The HTTP response body
 * @param int $code The HTTP response status code
 * @param string $charset The HTTP response charset
 *
 * @return int Returns 1, always
 */
function response_json($content, int $code = 200, array $headers = [], string $charset = 'utf-8')
{
    return response_json_str(json_encode($content), $code, $headers, $charset);
}

/**
 * Helper method to setup a header item as key-value parts.
 *
 * @param string $key The response header name
 * @param string $val The response header value
 * @param bool $replace Should replace a previous similar header, or add a second header of the same type.
 *
 * @return void
 */
function response_headers(array $headers): void
{
    foreach ($headers as $key => $val) {
        response_header($key, $val);
    }
}

/**
 * Helper method to setup a header item as key-value parts.
 *
 * @param string $key The response header name
 * @param string $val The response header value
 * @param bool $replace Should replace a previous similar header, or add a second header of the same type.
 *
 * @return void
 */
function response_header(string $key, string $val, bool $replace = true): void
{
    \header($key . ': ' . $val, $replace);
}

/**
 * Composes a default HTTP redirect response with the current base url.
 *
 * @param string $path
 *
 * @return void
 */
function response_redirect(string $path): void
{
    response_header('Location', url($path));
}

/**
 * Facade for No Content HTTP Responses.
 *
 * @return void
 */
function response_no_content(): void
{
    response_output();
}

/**
 * Enable CORS on SAPI.
 *
 * @param string $origin
 * @param string $headers
 * @param string $methods
 *
 * @return void
 */
function response_cors(string $origin = '*', string $headers = 'Content-Type', string $methods = 'GET, POST, PUT, DELETE', string $credentials = 'true'): void
{
    response_header('Access-Control-Allow-Origin', $origin);
    response_header('Access-Control-Allow-Headers', $headers);
    response_header('Access-Control-Allow-Methods', $methods);
    response_header('Access-Control-Allow-Credentials', $credentials);

    if (request_method_is('options')) {
        response_no_content();
    }
}

/**
 * Sugar for 404 Not found.
 *
 * @param string $content
 * @param string $charset
 * @return int
 */
function response_not_found(string $content = '', string $charset = 'utf-8'): int
{
    return response_output($content, 404, [], $charset);
}


function sapi_emit($response)
{
    $min_status_code_value = 100;
    $max_status_code_value = 599;

    $status = 200;

    if ($response instanceof Responsable) {
        file_put_contents('php://output', $response->getResponse());
        exit(0);
    }

    if ($response instanceof \Exception) {
        $status = $response->getCode();
        $response = $response->getMessage();
    }

    if ($status < $min_status_code_value) {
        $status = 200;
    }

    if ($status > $max_status_code_value) {
        $status = 500;
    }

    if (is_object($response) && method_exists($response, '__toString')) {
        $response = response_text($response->toString(), $status);
    }

    if (is_array($response) || is_object($response)) {
        $response = response_json($response, $status);
    }

    if (null === $response) {
        return;
    }

    if (is_callable($response)) {
        $response();
        return;
    }

    file_put_contents('php://output', $response);
}
