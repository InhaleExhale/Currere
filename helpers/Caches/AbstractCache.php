<?php

namespace Helpers\Cache;

abstract class AbstractCache {
    protected $cachePath;

    function __construct($cachePath=null) {
        $this->cachePath = $cachePath == null ?  \Config::get('cache/rootPath') : $cachePath;
    }

    abstract function check($activityId);
    abstract function write($activityId, $obj);
    abstract function read($activityId);
    abstract function delete($activityId);
    abstract function invalidate();
    abstract function clear();
}