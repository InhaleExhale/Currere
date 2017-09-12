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

    public function authorise() {
        if($this->connector->authorise()) {
            $token = $this->connector->loadToken();
            echo "<h2>GOT TOKEN: {$token}</h2>";
        }
    }

    public function deauthorise() {
        $this->connector->deauthorise();
    }

    public function complete() {
        $this->connector->complete();
        echo "<h2>Token complete</h2>";
    }

    public function defaultAction($params)
    {
        echo "DEFAULT AUTHENTICATION ACTION";
        // TODO: Implement defaultAction() method.
    }
}