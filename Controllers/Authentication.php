<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 19:22
 */

namespace Controllers;

use League\Plates\Engine;

class Authentication extends Base
{
    /// TODO: Controllers for actions relating to authentication. Should load the relevant connector and pass requests
    /// through to it
    private $connector;
    private $task;

    public function __construct($params) {
        parent::__construct();

        $connectorName = \Helpers\Request::get('connector', null);
        $this->connector = \Connectors\Factory::create($connectorName);
    }

    public function authenticate() {
        if($this->connector->authenticate()) {
            $token = $this->connector->loadToken();
            echo "<h2>TOKEN: {$token}</h2>";
        }
    }

    public function complete() {
        $token = $this->connector->getResponseToken();
        $this->connector->storeToken($token);
        echo "<h2>Token complete</h2>";
    }

    public function defaultAction($params)
    {
        echo "DEFAULT AUTHENTICATION ACTION";
        // TODO: Implement defaultAction() method.
    }

    public function getToken($params) {
        var_dump($this->connector->loadToken());

    }
}