<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 19:20
 */

namespace Helpers;


class Request
{
    public static function setController() {
        $path = self::get("path", false);

        if ($path) {
            $pieces = explode("/", $path);
            $_REQUEST["controller"] = ucwords($pieces[0]);

            if(count($pieces) > 1 && strlen($pieces[1]) > 0) {
                $_REQUEST["action"] = $pieces[1];;
            }

            $_REQUEST["actionParams"] = count($pieces) > 2 ? array_slice($pieces, 2) : array();
        }
    }

    static function get($key, $fallback) {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $fallback;
    }

}