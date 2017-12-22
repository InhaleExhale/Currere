<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 22:43
 */

namespace Controllers;


abstract class Base
{
    protected $templates;
    protected $requestParams;

    public function __construct()
    {
        $this->templates = \Helpers\Templates::get();

        $this->requestParams = \Helpers\Request::get('actionParams', array());
    }

    public function dispatch($task, $params = array())
    {
        $this->$task($params);
    }

    abstract function defaultAction($params);

    protected function done($result)
    {
        echo json_encode($result, JSON_PARTIAL_OUTPUT_ON_ERROR);
        die;
    }

    protected function param($paramName, $paramValue=false)
    {
        $paramId = array_search($paramName, $this->requestParams);
        if ($paramId !== false && count($this->requestParams) > ($paramId+1)) {
            $paramValue = $this->requestParams[$paramId + 1];
        }

        return $paramValue;
    }

    protected static function error($error) {
        error_log($error->getMessage());
        \Helpers\Helper::dump($error);
    }
}