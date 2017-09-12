<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 12/09/2017
 * Time: 08:33
 */

namespace Connectors\Strava;


use Connectors\ConnectorAuthenticator;

class Auth extends ConnectorAuthenticator
{

    private $tokenPath;
    private $api;
    protected $accessToken;


    public function __construct(\Iamstuartwilson\StravaApi $api)
    {
        $this->api = $api;
        $this->tokenPath = \Helpers\Path::join(array(__ROOT, 'Connectors', 'Strava', 'strava.token'));
    }

    public function authenticate()
    {
        if ($this->hasToken()) {
            return $this->loadToken();
        } else {
            $appRoot = \Config::get('core/appRoot');
            $redirectRawUrl = "http://{$_SERVER['HTTP_HOST']}{$appRoot}/" .
                "?controller=Authentication&connector=Strava&action=complete";

            $redirectUrl = \Helpers\Path::queryToUri($redirectRawUrl);

            $authUrl = $this->api->authenticationUrl($redirectUrl);
            \Helpers\Path::redirect($authUrl);
        }
        return false;
    }

    /// TODO: Eventually these should be stored against a user in the DB (encrypted), currently assume single user
    public function loadToken()
    {
        if ($this->hasToken()) {
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

    public function clearToken()
    {
        if ($this->hasToken()) {
            $this->api->deauthorize();
            unlink($this->tokenPath);
        }
    }

    /// TODO: This will need to change when we move to a database/user paradigm
    public function hasToken()
    {
        return file_exists($this->tokenPath);
    }

    public function setApiAccessToken()
    {
        $this->api->setAccessToken($this->accessToken);
    }

    public function getResponseToken()
    {
        $authCode = \Helpers\Request::get('code', null);
        if(is_null($authCode)) {
            throw new \Exception("Strava: Temporary access code not received.");
        }
        return $this->api->tokenExchange($authCode);
    }
}