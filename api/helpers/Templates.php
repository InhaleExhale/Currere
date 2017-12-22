<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 22:02
 */

namespace Helpers;

use League\Plates\Engine;

class Templates
{
    static $templates = null;
    static private $templateDirectory;

    static function get() {
        if(!self::$templates) {
            self::$templateDirectory = \Helpers\Path::join(array(__ROOT, 'templates'));
            self::$templates = new Engine(self::$templateDirectory);

            $extensionsPath = \Helpers\Path::join(array(self::$templateDirectory, 'extensions'));
            self::loadExtensions($extensionsPath);
        }
        return self::$templates;
    }

    static function loadExtensions($extensionsDirectory) {
        $dir = new \DirectoryIterator($extensionsDirectory);
        foreach ($dir as $fileinfo) {
            if (($fileinfo->isFile() && !$fileinfo->isDot())) {
                $filename = $fileinfo->getFilename();

                $filePath = \Helpers\Path::join(array($extensionsDirectory, $filename));
                require_once($filePath);

                $className = pathinfo($filePath)['filename'];
                $classNameWithNamespace = "\\League\\Plates\\Extension\\" . $className;
                self::$templates->loadExtension(new $classNameWithNamespace());
            }
        }

    }
}