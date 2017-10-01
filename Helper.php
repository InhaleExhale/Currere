<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 22:17
 */

namespace Helpers;

class Helper
{
    static $helpers = array();

    static function loadAll()
    {
        $helpersPath = __ROOT . DIRECTORY_SEPARATOR . 'helpers';
        foreach (new \DirectoryIterator($helpersPath) as $file) {
            if ($file->isFile() && !$file->isDot()) {
                $filename = $file->getFilename();
                $helperPath = $helpersPath . DIRECTORY_SEPARATOR . $filename;

                if (file_exists($helperPath)) {
                    self::$helpers[] = $filename;
                    require_once($helperPath);
                }
            }
        }
    }

    static function dump($obj)
    {
        echo '<pre>'; var_dump($obj); echo '</pre>';
    }
}