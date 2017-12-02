<?php
/**
 * User: tuttarealstep
 * Date: 04/11/17
 * Time: 10.42
 */

namespace HoneyCMS;

use ErrorException;
use Honey\Cache\SimpleCache;
use Honey\Cryptography\Cryptography;
use Honey\Log\Logger;
use Honey\Router\Router;
use Honey\Sessions\SessionsManager;
use Honey\Standards\Application as ApplicationStandard;
use HoneyCMS\Classes\ConfigurationsManager;
use HoneyCMS\Classes\Database;
use HoneyCMS\Classes\Settings;
use HoneyCMS\Classes\ThemesManager;
use HoneyCMS\Classes\Utils;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

final class Application implements ApplicationStandard
{
    /**
     * @var ConfigurationsManager
     */
    private static $configurationsManager;

    /**
     * @var Logger
     */
    private static $logger;

    /**
     * @var SessionsManager
     */
    private static $sessionsManager;

    /**
     * @var SimpleCache
     */
    private static $cacheManager;

    /**
     * @var Cryptography
     */
    private static $cryptography;

    /**
     * @var Router
     */
    private static $router;

    /**
     * @var \Honey\Database\Database
     */
    private static $database;

    /**
     * @var Settings
     */
    private static $settings;

    /**
     * @var ThemesManager
     */
    private static $themesManager;

    /**
     * @return ConfigurationsManager
     */
    public static function getConfigurationsManager()
    {
        return self::$configurationsManager;
    }

    /**
     * @return Cryptography
     */
    public static function getCryptography(): Cryptography
    {
        return self::$cryptography;
    }

    /**
     * @return \Honey\Database\Database
     */
    public static function getDatabase()
    {
        return self::$database;
    }

    /**
     * @return Router
     */
    public static function getRouter(): Router
    {
        return self::$router;
    }

    /**
     * @return Settings
     */
    public static function getSettings(): Settings
    {
        return self::$settings;
    }

    /**
     * @return SessionsManager
     */
    public static function getSessionsManager(): SessionsManager
    {
        return self::$sessionsManager;
    }

    /**
     * @return SimpleCache
     */
    public static function getCacheManager(): SimpleCache
    {
        return self::$cacheManager;
    }

    /**
     * @return ThemesManager
     */
    public static function getThemesManager(): ThemesManager
    {
        return self::$themesManager;
    }

    /**
     * @return string
     */
    public function version()
    {
        return '0.0.1';
    }

    public function initialize()
    {
        $this->initConfigurationsManager();
        $this->initLogger();
        $this->initErrorHandler();
        $this->initDebugMode();
        $this->initSessions();
        $this->initCache();
        $this->initCryptography();
        $this->initDatabase();
        $this->initSettings();
        $this->initDefines();
        $this->initRouter();
        $this->initThemesManager();
    }

    private function initConfigurationsManager()
    {
        self::$configurationsManager = new ConfigurationsManager();
    }

    private function initLogger()
    {
        $logger = new MonologLogger("Application");
        $logger->pushHandler(new StreamHandler(__DIR__ . "/Storage/Logs/application.log"));
        self::$logger = new Logger($logger);
    }

    private function initErrorHandler()
    {
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error["type"] == E_ERROR) {
                Application::logException(new \Exception($error["type"], $error["message"], $error["file"], $error["line"]));
            }
        });

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            Application::logException(new ErrorException($errstr, 0, $errno, $errfile, $errline));
        });

        set_exception_handler(function ($e) {
            Application::logException($e);
        });
    }

    public static function logException($e)
    {
        if (self::$configurationsManager->debug) {
            echo "<div style='text-align: center; font-family: Sans-Serif'>";
            echo "<h1 style='color: #e53935; text-transform: uppercase'>" . get_class($e) . " occured</h1>";
            echo "<table style='display: inline-block; border: 1px solid #212121; border-radius: 4px;'>";
            echo "<tr style='background-color:#eeeeee;'><th style='width: 100px;'>Type:</th><td>" . get_class($e) . "</td></tr>";
            echo "<tr style='background-color:#eeeeee;'><th>Message:</th><td>{$e->getMessage()}</td></tr>";
            echo "<tr style='background-color:#eeeeee;'><th>File:</th><td>{$e->getFile()}</td></tr>";
            echo "<tr style='background-color:#eeeeee;'><th>Line:</th><td>{$e->getLine()}</td></tr>";
            echo "</table></div>";
        }

        self::getLogger()->error("Error occured (type: " . get_class($e) . "). Message: " . $e->getMessage() . ". File: " . $e->getFile() . ". Line: " . $e->getLine() . ".");
        exit();
    }

    /**
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        return self::$logger;
    }

    private function initDebugMode()
    {
        if (self::$configurationsManager->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_PARSE | E_USER_ERROR | E_USER_WARNING);
            ini_set('display_errors', 'Off');
        }
    }

    private function initSessions()
    {
        self::$sessionsManager = new SessionsManager();
        self::$sessionsManager->startSession();
    }

    private function initCache()
    {
        self::$cacheManager = new SimpleCache(__DIR__ . "/Storage/Cache");
    }

    private function initCryptography()
    {
        self::$cryptography = new Cryptography(self::$configurationsManager->cryptography['key'], self::$configurationsManager->cryptography['cost'], self::$configurationsManager->cryptography['cipher']);
    }

    private function initDatabase()
    {
        $database = new Database();
        self::$database = $database->getDatabase();
    }

    private function initSettings()
    {
        self::$settings = new Settings();
    }

    private function initDefines()
    {
        if(self::$configurationsManager->timezone == null)
            self::$configurationsManager->timezone = self::$settings->getSetting('siteTimezone', "UTC");

        date_default_timezone_set(self::$configurationsManager->timezone);

        if(self::$configurationsManager->memoryLimit == null)
            @ini_set('memory_limit', self::$configurationsManager->memoryLimit != null ? self::$configurationsManager->memoryLimit : "64M");

        if(self::$configurationsManager->host == null)
            self::$configurationsManager->host = self::$settings->getSetting('siteUrl', Utils::resolveUrl());

        if(self::$configurationsManager->siteTheme == null)
            self::$configurationsManager->siteTheme = self::$settings->getSetting('siteTheme', "default");
    }

    private function initRouter()
    {
        self::$router = new Router();
        //todo add routes
    }

    private function initThemesManager()
    {
        self::$themesManager = new ThemesManager();
        self::$themesManager->initThemeFunctions();
    }

    public function run()
    {
        $this->sendResponse();
    }

    private function sendResponse()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        if(count(self::$router->getData()[0]) == 0)
        {
            throw new \Exception("No default route set!");
        }

        $routeInfo = self::$router->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Router::NOT_FOUND:
                //todo fix 404
                header("location: /");
                exit;
                break;
            case Router::METHOD_NOT_ALLOWED:
                //todo fix method not allowed
                $allowedMethods = $routeInfo[1];
                break;
            case Router::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                echo self::$themesManager->loadPage($handler, $vars);
                //todo load page
                //print_r($routeInfo);
                break;
        }
    }
}