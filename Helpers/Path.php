<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 22:11
 */

namespace Helpers {

    class Path
    {
        static function join($pieces)
        {
            if(!is_array($pieces)) {
                throw new \Exception("Path should be an array");
            }
            return join(DIRECTORY_SEPARATOR, $pieces);
        }

        static function queryToUri($query)
        {
            if (!\config::get('core/useSef')) {
                return \Config::get('core/appRoot').$query;
            } else {
                return '/SEF_NOT_IMPLEMENTED';
            }
        }
    }
}