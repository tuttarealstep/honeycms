<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 10.48
 */
namespace HoneyCMS\Classes;

use Honey\Config\ConfigContainer;

class ConfigurationsManager
{
    /**
     * @var ConfigContainer
     */
    private $configurations;

    public function __construct()
    {
        $this->configurations = new ConfigContainer();
        $this->loadGeneralConfigurations();
        $this->loadDatabaseConfigurations();
    }

    private function loadGeneralConfigurations()
    {
        $databaseConfigurations = require (__DIR__ . '/../Configurations/General.php');

        foreach ($databaseConfigurations as $configItemKey => $configItemValue)
        {
            $this->configurations->set($configItemKey, $configItemValue);
        }
    }

    private function loadDatabaseConfigurations()
    {
        $generalConfigurations = require (__DIR__ . '/../Configurations/Database.php');

        foreach ($generalConfigurations as $configItemKey => $configItemValue)
        {
            $this->configurations->set($configItemKey, $configItemValue);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->configurations->get($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->configurations->set($name, $value);
    }

    /**
     * @return ConfigContainer
     */
    public function getConfigurations(): ConfigContainer
    {
        return $this->configurations;
    }
}