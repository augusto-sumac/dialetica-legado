<?php


/**
 * Returns true if the current HTTP request is JSON (based on Content-type header).
 *
 * @param bool $default
 * @return bool
 */
function request_is_json(bool $default = false): bool
{
    $content_type = request_content_type();

    if ($content_type !== null) {
        return starts_with($content_type, 'application/json');
    }

    return $default;
}

/**
 * Returns true if the current request is multipart/form-data, based on Content-type header.
 *
 * @param bool $default
 * @return bool
 */
function request_is_multipart(bool $default = false): bool
{
    $content_type = request_content_type();

    if ($content_type !== null) {
        return starts_with($content_type, 'multipart/form-data');
    }

    return $default;
}

/**
 * Returns the Content-type header.
 *
 * @param string|null $default
 * @return string|null
 */
function request_content_type(?string $default = null): ?string
{
    return request_header('content-type', $default);
}

/**
 * Returns all the HTTP headers.
 *
 * @return array<string, string>
 */
function request_headers(): array
{
    /** @var array<string> $server_keys */
    $server_keys = array_keys($_SERVER);
    $http_headers = array_reduce(
        $server_keys,
        static function (array $headers, string $key): array {
            if ($key === 'CONTENT_TYPE') {
                $headers[] = $key;
            }

            if ($key === 'CONTENT_LENGTH') {
                $headers[] = $key;
            }

            if (strncmp($key, 'HTTP_', 5) === 0) {
                $headers[] = $key;
            }

            return $headers;
        },
        []
    );

    $values = array_map(static function (string $header): string {
        return (string)$_SERVER[$header];
    }, $http_headers);

    $headers = array_map(static function (string $header) {
        if (strncmp($header, 'HTTP_', 5) === 0) {
            $header = substr($header, 5);

            if ($header === false) {
                $header = 'HTTP_';
            }
        }

        return str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $header))));
    }, $http_headers);

    return array_combine($headers, $values);
}

/**
 * Returns the request header or the given default.
 *
 * @param string $key The header name
 * @param string|null $default The default value when header isn't present
 * @return string|null
 */
function request_header(string $key, ?string $default = null): ?string
{
    $val = array_get(request_headers(), $key, $default, true);

    if (is_array($val)) {
        return null;
    }

    return $val;
}

/**
 * Returns the current HTTP request method.
 * Override with X-Http-Method-Override header or _method on body.
 *
 * @return string
 */
function request_method(): string
{
    $method = request_header('X-Http-Method-Override');

    if ($method !== null) {
        return $method;
    }

    /**
     * @var array<string, string> $_POST
     * @var string|null $method
     */
    $method = array_get($_POST, '_method');

    if ($method !== null) {
        return $method;
    }

    /**
     * @var array<string, string> $_SERVER
     * @var string|null $method
     */
    $method = array_get($_SERVER, 'REQUEST_METHOD');

    return $method ?? 'GET';
}

/**
 * Checks for the current HTTP request method.
 *
 * @param string|string[] $method The given method to check on
 * @param string|null $request_method
 * @return bool
 */
function request_method_is($method, ?string $request_method = null): bool
{
    if ($request_method === null) {
        $request_method = request_method();
    }

    if (is_array($method)) {
        $method = array_map('strtolower', $method);

        return in_array(strtolower($request_method), $method, true);
    }

    return strtolower($method) === strtolower($request_method);
}
