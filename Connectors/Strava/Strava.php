<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:43
 */

namespace Connectors;

require_once(__ROOT . "/Connectors/Connector.php");
require_once(__ROOT . "/vendor/autoload.php");


use Iamstuartwilson\StravaApi;


class Strava extends Connector
{
    protected $accessToken;

    public function __construct($clientId, $clientSecret)
    {
        $this->api = new StravaApi(
            $clientId,
            $clientSecret
        );
    }

    public function authenticate()
    {
        $redirectUrl = '/?connector=strava&';
        //$this->api->authenticationUrl()

    }

    public function getActivities()
    {
        // TODO: Implement getActivities() method.
    }

    static function test() {
        var_dump(__FILE__);
    }

}