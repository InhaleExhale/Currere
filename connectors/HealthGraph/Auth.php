<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 12/09/2017
 * Time: 08:33
 */

namespace Connectors\HealthGraph;


use Connectors\ConnectorAuthenticator;

class Auth extends ConnectorAuthenticator
{

    private $tokenPath;
    private $api;
    protected $accessToken;

    public function __construct(\InhaleExhale\HealthGraphApi $api)
    {
        $this->api = $api;
        $this->tokenPath = \Helpers\Path::join(array(__ROOT, 'Connectors', 'HealthGraph', 'healthgraph.token'));
    }

    public function authenticationLink()
    {
        $appRoot = \Config::get('core/appRoot');
        $redirectRawUrl = "/?controller=Authentication&connector=HealthGraph&action=complete";

        $redirectUrl = \Helpers\Path::queryToUri($redirectRawUrl, true);

        return $this->api->authenticationUrl($redirectUrl);
    }

    /// TODO: Eventually these should be stored against a user in the DB (encrypted), currently assume single user
    public function loadToken()
    {
        if ($this->hasToken()) {
            $encryptedInfo= file_get_contents($this->tokenPath);
            $this->accessToken = json_decode(\Helpers\Token::decrypt($encryptedInfo));
            return $this->accessToken;
        }
        return false;
    }

    public function storeToken($rawToken)
    {
        $this->accessToken = $rawToken;
        $encryptedInfo = \Helpers\Token::encrypt(json_encode($rawToken));
        file_put_contents($this->tokenPath, $encryptedInfo);
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
            throw new \Exception("HealthGraph: Temporary access code not received.");
        }
        return $this->api->tokenExchange($authCode);
    }
}