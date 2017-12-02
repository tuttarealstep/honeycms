<?php
/**
 * User: tuttarealstep
 * Date: 05/11/17
 * Time: 12.16
 */

namespace HoneyCMS\Classes;

use HoneyCMS\Application;
use Honey\Template\Galaxy;
use Honey\Template\Simple;

class ThemesManager
{
    private $currentTheme;
    private $themeInfo;
    private $themeEngine;
    private $variables = [];

    function __construct()
    {
        $this->currentTheme = Application::getConfigurationsManager()->siteTheme;
        $this->initThemeInfo();
        $this->checkThemeInfo();
    }

    /**
     * Load theme functions
     */
    public function initThemeFunctions()
    {
        $functionsFile = Utils::resolveThemePath($this->currentTheme) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "functions.php";

        if(file_exists($functionsFile))
        {
            require_once $functionsFile;
        }
    }

    /**
     * Load theme information
     */
    private function initThemeInfo()
    {
        $infoFile = Utils::resolveThemePath($this->currentTheme) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "info.php";

        if(file_exists($infoFile))
        {
            $themeInfo = @require_once $infoFile;
            if(is_array($themeInfo))
            {
                $this->themeInfo = $themeInfo;
            }
        }
    }

    private function checkThemeInfo()
    {
        //todo add example of these settings!
        if(!isset($this->themeInfo['themeEngine']))
            $this->themeInfo['themeEngine'] = "mixed";

        if(!isset($this->themeInfo['useCache']))
            $this->themeInfo['useCache'] = false;

        if(!isset($this->themeInfo['cachePath']))
            $this->themeInfo['cachePath'] = ROOT_PATH . "/Storage/Cache";
    }

    public function loadPage($handler, $vars, $engine = null)
    {
        if($engine == null)
            $engine = $this->themeInfo['themeEngine'];

        switch ($engine)
        {
            case "galaxy":
                $this->themeEngine = new Galaxy\Core(["cache" => $this->themeInfo['useCache'], 'cachePath' => $this->themeInfo['cachePath']]);
                $this->themeEngine->setVariables($this->variables);
                return $this->themeEngine->compileFile(THEMES_PATH . "/" . $this->getCurrentTheme() . "/" . $handler);
                break;
            case "simple":
                $this->themeEngine = new Simple\Core(["cache" => $this->themeInfo['useCache'], 'cachePath' => $this->themeInfo['cachePath']]);
                $this->themeEngine->setVariables($this->variables);
                return $this->themeEngine->compileFile(THEMES_PATH . "/" . $this->getCurrentTheme() . "/" . $handler);
                break;
            case "mixed":
                $fileInfo = explode(".", $handler);
                $extension = $fileInfo[count($fileInfo) - 1];
                switch ($extension)
                {
                    case "galaxy":
                        return $this->loadPage($handler, $vars, "galaxy");
                        break;
                    default:
                        return $this->loadPage($handler, $vars, "simple");
                }
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentTheme()
    {
        return $this->currentTheme;
    }

    /**
     * @return mixed
     */
    public function getThemeInfo()
    {
        return $this->themeInfo;
    }

    public function addVariable($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function getThemeEngine()
    {
        return $this->themeEngine;
    }
}