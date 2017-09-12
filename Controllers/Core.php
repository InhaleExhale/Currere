<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 21:18
 */

namespace Controllers;

use League\Plates\Engine;

class Core extends Base
{
    public function __construct($params) {
        parent::__construct();
    }

    public function defaultAction($params) {

        $connectors = \Connectors\Factory::createAll();
        echo $this->templates->render('core/home', ['connectors' => $connectors]);
    }
}