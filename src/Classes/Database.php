<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 12.43
 */

namespace HoneyCMS\Classes;

use Honey\Database\Connectors\MySqlConnector;
use Honey\Database\MySqlDatabase;
use HoneyCMS\Application;

class Database
{
    /**
     * @var \Honey\Database\Database
     */
    private $database;

    /**
     * @var ConfigurationsManager
     */
    protected $configurations;

    function __construct()
    {
        $this->configurations = Application::getConfigurationsManager();
        $this->setUp();
    }

    private function setUp()
    {
        try {
            $mysqlConnector = new MySqlConnector();
            $this->database = new MySqlDatabase($mysqlConnector->connect([
                    'host' => $this->configurations->database["host"],
                    'charset' => $this->configurations->database["charset"],
                    'database' => $this->configurations->database["name"],
                    'username' => $this->configurations->database["username"],
                    'password' => $this->configurations->database["password"]
                ]
            ));
        } catch (\Exception $e)
        {
            $this->database = null;
            if(!Application::getConfigurationsManager()->survivalMode)
            {
                throw new \ErrorException($e->getMessage(), $e->getCode(), 1, $e->getFile(), $e->getLine());
            } else {
                Application::getLogger()->error($e);
            }
        }
    }
    /**
     * @return \Honey\Database\Database
     */
    public function getDatabase()
    {
        return $this->database;
    }
}