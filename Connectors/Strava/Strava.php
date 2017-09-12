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

    public function authoriseLink()
    {
        return $this->authenticator->authenticationLink();
    }

    public function complete()
    {
        $token = $this->authenticator->getResponseToken();
        $this->authenticator->storeToken($token);
        \Helpers\Path::redirect('/', true);
    }

    public function authorise()
    {
        if($this->authenticator->hasToken()) {
            $this->authenticator->loadToken();
            $this->authenticator->setApiAccessToken();
            return true;
        } else {
            return false;
        }
    }

    public function deauthorise()
    {
        $this->authenticator->clearToken();
        \Helpers\Path::redirect('/', true);
    }


    public function isAuthorised() {
        return $this->authenticator->hasToken();
    }

    public function getActivities()
    {
        // TODO: Implement getActivities() method.
        $activities = $this->api->get('athlete/activities',['per_page'=>100]);
        return $activities;
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