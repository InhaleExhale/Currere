<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 01/10/2017
 * Time: 15:40
 */

namespace Models;


abstract class Model
{
    protected $id;
    protected $_db;

    function __construct()
    {
        $this->_db = \Database\Factory::getInstance();
    }

    abstract public function bind($data);

    abstract public function load($id);

    abstract public function save();

    abstract public function insert();

    abstract public function update();

    abstract public function delete($id);
}

class Factory
{
    static $models = array();

    static function getList()
    {
        return self::$models;
    }

    static function requireAll($load = true)
    {
        $modelRoot = \Helpers\Path::join(array(__ROOT, "models"));

        $dirs = array();
        foreach (new \DirectoryIterator($modelRoot) as $file) {
            if ($file->isFile() && !$file->isDot() && self::isModel($file->getBasename('.php'))) {
                $filename = $file->getFilename();
                if ($load) {
                    $modelPath = \Helpers\Path::join(array($modelRoot, $filename));
                    self::$models[] = $file->getBasename('.model.php');
                    require_once($modelPath);
                }
            }
        }
        return $dirs;
    }

    static function isModel($filename)
    {
        $pieces = explode('.', $filename);
        return array_pop($pieces) == 'model';
    }

    static function create($type, $options = array())
    {
        if (!in_array($type, self::$models)) {
            throw new \Exception("Model not found: " . $type);
        }
        $type = "\\Connectors\\{$type}";
        return $type::create($options);
    }
}