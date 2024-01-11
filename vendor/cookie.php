<?php

/**
 * This file is part of the Force Components.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Cookie
{
    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     * Send a cookie
     *
     *  <code>
     *      Cookie::set('limit', 10);
     *  </code>
     *
     * @param  string  $key      A name for the cookie.
     * @param  mixed   $value    The value to be stored. Keep in mind that they will be serialized.
     * @param  integer $expire   The number of seconds that this cookie will be available.
     * @param  string  $path     The path on the server in which the cookie will be availabe. Use / for the entire domain, /foo if you just want it to be available in /foo.
     * @param  string  $domain   The domain that the cookie is available on. Use .example.com to make it available on all subdomains of example.com.
     * @param  boolean $secure   Should the cookie be transmitted over a HTTPS-connection? If true, make sure you use a secure connection, otherwise the cookie won't be set.
     * @param  boolean $httpOnly Should the cookie only be available through HTTP-protocol? If true, the cookie can't be accessed by Javascript, ...
     * @return boolean
     */
    public static function set($key, $value, $expire = 86400, $domain = '', $path = '/', $secure = false, $httpOnly = false)
    {
        // Redefine vars
        $key      = (string) $key;
        $value    = serialize($value);
        $expire   = time() + (int) $expire;
        $path     = (string) $path;
        $domain   = (string) $domain;
        $secure   = (bool) $secure;
        $httpOnly = (bool) $httpOnly;

        // Set cookie
        return setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Store a cookie for a long, long time
     *
     * @param string $cookieName
     * @param string $cookieValue
     *
     * @return bool
     */
    public static function forever($key, $value)
    {
        return self::set($key, $value, 525600 * 5); // 525600 minutes = 1 year
    }

    /**
     * Get a cookie
     *
     *  <code>
     *      $limit = Cookie::get('limit');
     *  </code>
     *
     * @param  string $key The name of the cookie that should be retrieved.
     * @return mixed
     */
    public static function get($key)
    {
        // Redefine key
        $key = (string) $key;

        // Cookie doesn't exist
        if (!isset($_COOKIE[$key])) {
            return false;
        }

        // Fetch base value
        $value = function_exists('get_magic_quotes_gpc') ? stripslashes($_COOKIE[$key]) : $_COOKIE[$key];

        $json_value = (array)@json_decode($value);

        if ($json_value) {
            return $json_value;
        }

        // Unserialize
        $actual_value = @unserialize($value);

        // If unserialize failed
        if ($actual_value === false && serialize(false) != $value) {
            return false;
        }

        // Everything is fine
        return $actual_value;
    }


    /**
     * Delete a cookie
     *
     *  <code>
     *      Cookie::delete('limit');
     *  </code>
     *
     * @param string $name The name of the cookie that should be deleted.
     */
    public static function delete($key)
    {
        unset($_COOKIE[$key]);
    }
}
