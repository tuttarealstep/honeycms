<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 17.29
 */

namespace HoneyCMS\Classes;

use HoneyCMS\Application;

class Settings
{
    public function setSetting($name, $value)
    {
        if (Application::getDatabase() == null)
            return;

        if ($this->getSetting($name) == null)
        {
            Application::getDatabase()->table("honey_settings")->insert(["name" => $name, "value" => $value]);
        }
    }

    /**
     * @param $name
     * @param null $default
     * @return string
     */
    public function getSetting($name, $default = null)
    {
        if(Application::getDatabase() == null)
            return $default;

        try
        {
            $value = Application::getDatabase()->table("honey_settings")->where("name", $name)->first();
            if(isset($value->value))
            {
                $value = $value->value;
            } else {
                $value = $default;
            }
        } catch (\Exception $e)
        {
            //todo handle error
            $value = $default;
        }

        return $value;
    }
}