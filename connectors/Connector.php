<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 20:13
 */

namespace Connectors;
require_once(__ROOT . "/helpers/Path.php");

abstract class ConnectorAuthenticator
{

    protected $accessToken;

    abstract function loadToken();
    abstract function storeToken($rawToken);
    abstract function clearToken();
    abstract function setApiAccessToken();
    abstract function getResponseToken();
}

abstract class Connector
{

    protected $activities;
    protected $authenticator;

    abstract function authorise();
    abstract function deauthorise();

    public function getClass($excludeNamespace=false) {
        $class = get_class($this);
        if($excludeNamespace) {
            $pieces = explode('\\', $class);
            return end($pieces);
        } else {
            return $class;
        }
    }
}

class Factory
{
    static $connectors = array();

    static function getList() {
        return self::$connectors;
    }

    static function requireAll($load = true)
    {
        $connectorPath = \Helpers\Path::join(array(__ROOT, "connectors"));

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

    static function createAll() {
        return array_map(function($connectorType) {
            return self::create($connectorType);
        }, self::$connectors);
    }
}