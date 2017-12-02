<?php
/**
 * User: tuttarealstep
 * Date: 05/11/17
 * Time: 11.53
 */
namespace HoneyCMS\Classes;

class Utils
{
    /**
     * Return the current url with http or https
     * @return string
     */
    static function resolveUrl()
    {
      return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME']
      );
    }

    /**
     * Check if the cms is running in the console
     * @return bool
     */
    static function checkConsole()
    {
        return php_sapi_name() == "cli" ? true : false;
    }

    /**
     * @param $themeName
     * @return string
     */
    static function resolveThemePath($themeName)
    {
        $themePath = THEMES_PATH . DIRECTORY_SEPARATOR . $themeName;

        if(file_exists($themePath))
        {
            return $themePath;
        } else {
            return THEMES_PATH . DIRECTORY_SEPARATOR . "default";
        }
    }
}