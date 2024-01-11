<?php

class Crypter
{
    public static function key()
    {
        return $GLOBALS['config']['app_key'];
    }

    public static function encrypt(string $data)
    {
        $iv_len = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($iv_len);
        $encrypted = openssl_encrypt($data, $cipher, static::key(), OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $encrypted, static::key(), true);
        return base64_encode($iv . $hmac . $encrypted);
    }

    public static function decrypt($data)
    {
        $encrypted = base64_decode($data);
        $iv_len = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($encrypted, 0, $iv_len);
        $hmac = substr($encrypted, $iv_len, $sha2len = 32);
        $raw_data = substr($encrypted, $iv_len + $sha2len);
        $original = openssl_decrypt($raw_data, $cipher, static::key(), OPENSSL_RAW_DATA, $iv);
        $calc_mac = hash_hmac('sha256', $raw_data, static::key(), true);
        return hash_equals($hmac, $calc_mac) ? $original : null;
    }
}
