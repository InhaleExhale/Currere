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
            return join(DIRECTORY_SEPARATOR, $pieces);
        }

        static function queryToUri($query)
        {
            if (!\config::get('core/useSef')) {
                return $query;
            } else {
                return '/SEF_NOT_IMPLEMENTED';
            }
        }
    }
}