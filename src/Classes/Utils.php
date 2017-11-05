<?php
/**
 * User: tuttarealstep
 * Date: 05/11/17
 * Time: 11.53
 */
namespace HoneyCMS\Classes;

class Utils
{
    static function resolveUrl()
    {
      return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME']
      );
    }
}