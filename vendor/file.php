<?php

class File
{

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public static function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * <code>
     *		// Get the contents of a file
     *		$contents = File::get(path('app').'routes'.EXT);
     *
     *		// Get the contents of a file or return a default value if it doesn't exist
     *		$contents = File::get(path('app').'routes'.EXT, 'Default Value');
     * </code>
     *
     * @param  string  $path
     * @param  mixed   $default
     * @return string
     */
    public static function get($path, $default = null)
    {
        return (file_exists($path)) ? file_get_contents($path) : value($default);
    }

    /**
     * Write to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function put($path, $data)
    {
        return file_put_contents($path, $data, LOCK_EX);
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function append($path, $data)
    {
        return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
    }

    /**
     * Delete a file.
     *
     * @param  string  $path
     * @return bool
     */
    public static function delete($path)
    {
        if (self::exists($path)) return @unlink($path);
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return void
     */
    public static function move($path, $target)
    {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return void
     */
    public static function copy($path, $target)
    {
        return copy($path, $target);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public static function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string  $path
     * @return string
     */
    public static function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public static function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public static function modified($path)
    {
        return filemtime($path);
    }

    /**
     * Get a file MIME type by extension.
     *
     * <code>
     *		// Determine the MIME type for the .tar extension
     *		$mime = File::mime('tar');
     *
     *		// Return a default value if the MIME can't be determined
     *		$mime = File::mime('ext', 'application/octet-stream');
     * </code>
     *
     * @param  string  $extension
     * @param  string  $default
     * @return string
     */
    public static function mime($extension, $default = 'application/octet-stream')
    {
        $mimes = self::mimes();

        if (!array_key_exists($extension, $mimes)) return $default;

        return (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
    }

    /**
     * Determine if a file is of a given type.
     *
     * The Fileinfo PHP extension is used to determine the file's MIME type.
     *
     * <code>
     *		// Determine if a file is a JPG image
     *		$jpg = File::is('jpg', 'path/to/file.jpg');
     *
     *		// Determine if a file is one of a given list of types
     *		$image = File::is(array('jpg', 'png', 'gif'), 'path/to/file');
     * </code>
     *
     * @param  array|string  $extensions
     * @param  string        $path
     * @return bool
     */
    public static function is($extensions, $path)
    {
        $mimes = self::mimes();

        $mime = null;

        if (function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')) {
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        } else if (function_exists('mime_content_type')) {
            $mime = mime_content_type($path);
        }

        // The MIME configuration file contains an array of file extensions and
        // their associated MIME types. We will loop through each extension the
        // developer wants to check and look for the MIME type.
        foreach ((array) $extensions as $extension) {
            if (isset($mimes[$extension]) and in_array($mime, (array) $mimes[$extension])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a new directory.
     *
     * @param  string  $path
     * @param  int     $chmod
     * @return void
     */
    public static function mkdir($path, $chmod = 0777)
    {
        if (strpos($path, 'http') !== false) {
            return true;
        }
        return (!is_dir($path)) ? mkdir($path, $chmod, true) : true;
    }

    /**
     * Move an uploaded file to permanent storage.
     *
     * This method is simply a convenient wrapper around move_uploaded_file.
     *
     * <code>
     *		// Move the "picture" file to a new permanent location on disk
     *		File::upload('picture', 'path/to/photos', 'picture.jpg');
     * </code>
     *
     * @param  string  $key
     * @param  string  $directory
     * @param  string  $name
     * @return string $target filename
     */
    public static function upload($key, $directory, $name = null)
    {
        $file = array_get($_FILES, $key);

        if (is_null($file))
            return false;

        if (is_uploaded_file(array_get($file, 'tmp_name'))) {
            if (!is_dir($directory)) {
                if (false === self::mkdir($directory)) {
                    throw new Exception(sprintf('Unable to create the "%s" directory', $directory));
                }
            } elseif (!is_writable($directory)) {
                throw new Exception(sprintf('Unable to write in the "%s" directory', $directory));
            }

            $target = rtrim($directory, DS) . DS . (null === $name ? array_get($file, 'name') : $name);

            if (!@move_uploaded_file(array_get($file, 'tmp_name'), $target)) {
                $error = error_get_last();
                throw new Exception(sprintf('Could not move the file "%s" to "%s" (%s)', array_get($file, 'tmp_name'), $target, strip_tags($error['message'])));
            }

            @chmod($target, 0777 & ~umask());

            return $target;
        } else {
            return false;
        }
    }

    public static function mimes($mime = null)
    {
        /**
         * Mime types
         */
        $mimes = array(
            'hqx'   => 'application/mac-binhex40',
            'cpt'   => 'application/mac-compactpro',
            'csv'   => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream'),
            'bin'   => 'application/macbinary',
            'dms'   => 'application/octet-stream',
            'lha'   => 'application/octet-stream',
            'lzh'   => 'application/octet-stream',
            'exe'   => array('application/octet-stream', 'application/x-msdownload'),
            'class' => 'application/octet-stream',
            'psd'   => 'application/x-photoshop',
            'so'    => 'application/octet-stream',
            'sea'   => 'application/octet-stream',
            'dll'   => 'application/octet-stream',
            'oda'   => 'application/oda',
            'pdf'   => array('application/pdf', 'application/x-download'),
            'ai'    => 'application/postscript',
            'eps'   => 'application/postscript',
            'ps'    => 'application/postscript',
            'smi'   => 'application/smil',
            'smil'  => 'application/smil',
            'mif'   => 'application/vnd.mif',
            'xls'   => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
            'ppt'   => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
            'wbxml' => 'application/wbxml',
            'wmlc'  => 'application/wmlc',
            'dcr'   => 'application/x-director',
            'dir'   => 'application/x-director',
            'dxr'   => 'application/x-director',
            'dvi'   => 'application/x-dvi',
            'gtar'  => 'application/x-gtar',
            'gz'    => 'application/x-gzip',
            'php'   => array('application/x-httpd-php', 'text/x-php'),
            'php4'  => 'application/x-httpd-php',
            'php3'  => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'phps'  => 'application/x-httpd-php-source',
            'js'    => 'application/x-javascript',
            'swf'   => 'application/x-shockwave-flash',
            'sit'   => 'application/x-stuffit',
            'tar'   => 'application/x-tar',
            'tgz'   => array('application/x-tar', 'application/x-gzip-compressed'),
            'xhtml' => 'application/xhtml+xml',
            'xht'   => 'application/xhtml+xml',
            'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
            'mid'   => 'audio/midi',
            'midi'  => 'audio/midi',
            'mpga'  => 'audio/mpeg',
            'mp2'   => 'audio/mpeg',
            'mp3'   => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
            'aif'   => 'audio/x-aiff',
            'aiff'  => 'audio/x-aiff',
            'aifc'  => 'audio/x-aiff',
            'ram'   => 'audio/x-pn-realaudio',
            'rm'    => 'audio/x-pn-realaudio',
            'rpm'   => 'audio/x-pn-realaudio-plugin',
            'ra'    => 'audio/x-realaudio',
            'rv'    => 'video/vnd.rn-realvideo',
            'wav'   => 'audio/x-wav',
            'bmp'   => 'image/bmp',
            'gif'   => 'image/gif',
            'jpeg'  => array('image/jpeg', 'image/pjpeg'),
            'jpg'   => array('image/jpeg', 'image/pjpeg'),
            'jpe'   => array('image/jpeg', 'image/pjpeg'),
            'png'   => 'image/png',
            'tiff'  => 'image/tiff',
            'tif'   => 'image/tiff',
            'css'   => 'text/css',
            'html'  => 'text/html',
            'htm'   => 'text/html',
            'shtml' => 'text/html',
            'txt'   => 'text/plain',
            'text'  => 'text/plain',
            'log'   => array('text/plain', 'text/x-log'),
            'rtx'   => 'text/richtext',
            'rtf'   => 'text/rtf',
            'xml'   => 'text/xml',
            'xsl'   => 'text/xml',
            'mpeg'  => 'video/mpeg',
            'mpg'   => 'video/mpeg',
            'mpe'   => 'video/mpeg',
            'qt'    => 'video/quicktime',
            'mov'   => 'video/quicktime',
            'avi'   => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'doc'   => 'application/msword',
            'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'word'  => array('application/msword', 'application/octet-stream'),
            'xl'    => 'application/excel',
            'eml'   => 'message/rfc822',
            'json'  => array('application/json', 'text/json'),
        );

        return array_get($mimes, $mime, $mimes);
    }
}
