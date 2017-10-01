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

    public function __construct()
    {
        $this->templates = \Helpers\Templates::get();
    }

    public function dispatch($task, $params = array())
    {
        $this->$task($params);
    }

    abstract function defaultAction($params);
}