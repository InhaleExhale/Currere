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

    abstract function getActivities();
}

class Factory
{

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

                        if(file_exists($helperPath)) {
                            require_once($helperPath);
                        } else {
                            trigger_error("{$helperPath}: Connector file not found", E_USER_WARNING);
                        }
                }
            }
        }

        return $dirs;
    }

    static function create($type, $params) {
        return $type::create($params);
    }
}