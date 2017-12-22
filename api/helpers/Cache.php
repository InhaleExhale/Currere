<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 20/12/2017
 * Time: 10:11
 */

namespace Helpers\Cache;

use Throwable;

class CacheHitException extends \Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class CacheFactory {
    static $caches = array();

    public static function get($type, $path = null) {
        $type = ucwords($type);
        if(!array_key_exists($type, self::$caches)) {
            $cacheClass = $type . "Cache";
            require_once('Caches' . DIRECTORY_SEPARATOR . $cacheClass . ".php");

            $namespacedCacheClass = "\\Helpers\\Cache\\$cacheClass";
            self::$caches[$type] = new $namespacedCacheClass($path);
        }

        return self::$caches[$type];
    }
}