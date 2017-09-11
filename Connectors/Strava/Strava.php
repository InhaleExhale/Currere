<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:43
 */

namespace Connectors;

require_once(__ROOT . "/Connectors/Connector.php");
use Iamstuartwilson\StravaApi;

class Strava extends Connector
{
    private $tokenPath;
    protected $accessToken;
    private $api;

    public function __construct($params, $options = null)
    {
        $this->tokenPath = \Helpers\Path::join(array(__ROOT, 'Connectors', 'Strava', 'strava.token'));

        $this->api = new StravaApi(
            $params['clientId'],
            $params['clientSecret']
        );
    }

    public function authenticate()
    {
        if ($this->loadToken()) {
            return true;
        } else {
            var_dump("Authenticating");
            $redirectUrl = "http://{$_SERVER['HTTP_HOST']}/Currere/?controller=Authentication&connector=Strava&action=complete";
            echo "<a href=\"{$this->api->authenticationUrl($redirectUrl)}\">Connect...</a>";
        }
        return false;
    }

    /// TODO: Eventually these should be stored against a user in the DB (encrypted), currently assume single user
    public function loadToken()
    {
        if (file_exists($this->tokenPath)) {
            $encryptedToken = file_get_contents($this->tokenPath);
            $this->accessToken = \Helpers\Token::decrypt($encryptedToken);
            return $this->accessToken;
        }
        return false;
    }

    public function storeToken($rawToken)
    {
        $this->accessToken = $rawToken;
        $encryptedToken = \Helpers\Token::encrypt($rawToken);
        file_put_contents($this->tokenPath, $encryptedToken);
    }

    public function getResponseToken()
    {
        return \Helpers\Request::get('code', null);
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