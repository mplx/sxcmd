<?php
/**
 * sxcmd
 * simple commandline interface to a skylable sx cluster
 *
 * @package     sxcmd
 * @author      Martin Pircher <mplx+coding@donotreply.at>
 * @copyright   Copyright (c) 2014-2015, Martin Pircher
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 **/

namespace mplx\sxcmd;

/**
* Utils
*/
class Util
{
    /**
    * binaryprefix SI units
    *
    * @param mixed $size
    * @param boolean $iec
    * @return string
    */
    public static function prettyBytes($size, $iec = false)
    {
        $i=0;
        if ($iec) {
            $sizetype = array ('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        } else {
            $sizetype = array ('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        }

        while ($size > 1024) {
            $size = $size / 1024;
            $i++;
        }
        $size = ceil($size);
        return $size . ' ' . $sizetype[$i];
    }

    /**
    * reverse IP lookup
    *
    * @param string $ip target IP
    * @param boolean $lookup really do a lookup?
    * @return string
    */
    public static function rDns($ip, $lookup = true)
    {
        return $lookup ?gethostbyaddr($ip) : $ip;
    }

    /**
    * return sxcmd home directory
    *
    * @return string
    */
    public static function getSxCmdDir()
    {
        if (isset($_SERVER['HOME'])) {
            return $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.sxcmd' . DIRECTORY_SEPARATOR;
        } elseif (isset($_SERVER['LOCALAPPDATA'])) {
            return
                $_SERVER['LOCALAPPDATA'] . DIRECTORY_SEPARATOR .
                'mplx' . DIRECTORY_SEPARATOR .
                'sxcmd' . DIRECTORY_SEPARATOR;
        } else {
            return '.' . DIRECTORY_SEPARATOR;
        }
    }

    /**
    * shorten auth key to 22 characters
    *
    * @param string $key
    * @return string
    */
    public static function shortenKey($key)
    {
        return substr($key, 0, 10) . '..' . substr($key, -10, 10);
    }
}
