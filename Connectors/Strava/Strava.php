<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:43
 */

namespace Connectors;

require_once(__ROOT . "/Connectors/Connector.php");
require_once("Auth.php");
use Iamstuartwilson\StravaApi;

class Strava extends Connector
{
    const name = "Strava Connector";
    const version = "0.0.1";

    protected $authenticator;
    private $api;

    public function __construct($params, $options = null)
    {
        $this->api = new StravaApi(
            $params['clientId'],
            $params['clientSecret']
        );

        $this->authenticator = new Strava\Auth($this->api);
    }

    public function authorise()
    {
        if($this->authenticator->authenticate()) {
            $this->authenticator->setApiAccessToken();
        }
    }

    public function deauthorise()
    {
        $this->authenticator->clearToken();
    }

    public function isauthorised() {
        return $this->authenticator->hasToken();
    }

    public function getActivities()
    {
        // TODO: Implement getActivities() method.
    }

    static function test()
    {
        var_dump(__FILE__);
    }

    static function create($options)
    {
        $params = array(
            'clientId' => \Config::get('strava/clientId'),
            'clientSecret' => \Config::get('strava/clientSecret')
        );

        return new Strava($params, $options);
    }

}