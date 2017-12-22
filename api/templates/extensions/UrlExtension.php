<?php

namespace League\Plates\Extension;

use League\Plates\Engine;

class UrlExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('queryToUri', [$this, 'queryToUri']);
    }

    public function queryToUri($queryUrl)
    {
        return \Helpers\Path::queryToUri($queryUrl);
    }

}

