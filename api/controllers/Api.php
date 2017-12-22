<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 22/12/2017
 * Time: 12:34
 */

namespace controllers;
namespace Controllers;

class Api extends Base
{

    function defaultAction($params)
    {

    }

    private function done($data) {
        echo $this->templates->render('core/app', ['connectors' => $connectors]);

    }
}