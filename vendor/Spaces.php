<?php

require_once(__DIR__ . '/Spaces/Spaces.php');

class Spaces
{
    public static $connection = null;

    public static function connection(): SpacesConnect
    {
        if (!static::$connection) {
            $key    = config('spaces.key');
            $secret = config('spaces.secret');
            $space  = config('spaces.space');
            $region = config('spaces.region');

            static::$connection = new SpacesConnect($key, $secret, $space, $region);;
        }

        return static::$connection;
    }

    public static function ls()
    {
        return static::connection()->ListObjects();
    }

    public static function upload($file, $saveAs = '', $access = 'public')
    {
        $ext = File::extension($file);
        $mime = File::mime($ext);

        return static::connection()->UploadFile($file, $access, $saveAs, $mime);
    }

    public static function uploadDirectory($directory, $keyPrefix = "")
    {
        return static::connection()->UploadDirectory($directory, $keyPrefix);
    }

    public static function deleteFile($file_path)
    {
        return static::connection()->DeleteObject($file_path);
    }

    public static function getFile($file_path)
    {
        return static::connection()->GetObject($file_path);
    }

    public static function getUrl($file_path)
    {
        return static::connection()->GetUrl($file_path);
    }
}
