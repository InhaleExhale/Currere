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
    static function get($key, $fallback) {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $fallback;
    }

}