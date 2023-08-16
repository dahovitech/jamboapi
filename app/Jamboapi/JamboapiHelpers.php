<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jamboapi;

class JamboapiHelpers
{
    public static function getUploadMaxFileSize()
    {
        $php_post_max_size = self::returnBytes(ini_get('post_max_size'));
        $php_upload_max_filesize = self::returnBytes(ini_get('upload_max_filesize'));
        $env_max_file_size = self::returnBytes(env('MAX_FILE_SIZE'));

        if ($php_post_max_size < $php_upload_max_filesize) {
            $max_file_size = $php_post_max_size;
        } else {
            if ($php_upload_max_filesize < $env_max_file_size) {
                $max_file_size = $php_upload_max_filesize;
            } else {
                $max_file_size = $env_max_file_size;
            }
        }

        return $max_file_size;
    }

    /**
     * Return size in bytes
     *
     * @param  string  $val
     * @return int $val
     */
    private static function returnBytes($val)
    {
        if (empty($val)) {
            return 0;
        }

        $val = trim($val);

        preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

        $last = '';
        if (isset($matches[2])) {
            $last = $matches[2];
        }

        if (isset($matches[1])) {
            $val = (int) $matches[1];
        }

        switch (strtolower($last)) {
            case 'g':
            case 'gb':
                $val *= 1024;
            case 'm':
            case 'mb':
                $val *= 1024;
            case 'k':
            case 'kb':
                $val *= 1024;
        }

        return (int) $val;
    }

    public static function getSupportedFileTypes()
    {
        return env('SUPPORTED_FILE_TYPES');
    }

    public static function getSupportedFileMimes()
    {
        return env('SUPPORTED_FILE_MIMES');
    }
}
