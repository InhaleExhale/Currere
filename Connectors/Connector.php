<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 20:13
 */

namespace Connectors;
require_once(__ROOT . "/Helpers/Path.php");

abstract class Connector
{

    protected $activities;
    protected $accessToken;

    abstract function authenticate();
    abstract function loadToken();
    abstract function storeToken($rawToken);
    abstract function getResponseToken();
}

class Factory
{
    static $connectors = array();

    static function requireAll($load = true)
    {
        $connectorPath = \Helpers\Path::join(array(__ROOT, "Connectors"));

        $dirs = array();
        foreach (new \DirectoryIterator($connectorPath) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $filename = $file->getFilename();
                $dirs[] = $filename;
                if ($load) {
                    $helperPath = \Helpers\Path::join(array(
                        $connectorPath,
                        $filename,
                        "{$filename}.php"
                    ));

                    if (file_exists($helperPath)) {
                        self::$connectors[] = $filename;
                        require_once($helperPath);
                    } else {
                        trigger_error("{$helperPath}: Connector file not found", E_USER_WARNING);
                    }
                }
            }
        }

        return $dirs;
    }

    static function create($type, $options = array())
    {
        if (!in_array($type, self::$connectors)) {
            throw new \Exception("Connector not found: " . $type);
        }
        $type = "\\Connectors\\{$type}";
        return $type::create($options);
    }
}