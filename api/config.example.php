<?php
/**
 * Rename this file to config.php and change templated values to match your ones.
 */

class Config
{
    public static $config = array(
        "core" => array(
            "useSef" => false,
            "appSecret" => "hello_goodbye_1024",
            "appRoot" => "/Currere",
            "defaultConnector" => "Strava"
        ),
        "cache" => array(
            "rootPath" => (ROOT_PATH . DIRECTORY_SEPARATOR . "cache"),
            "expirySeconds" => 30*24*60*60
        ),
        "database" => array(
            "host" => "<DB_HOST>",
            "name" => "<DB_NAME>",
            "username" => "<USERNAME>",
            "password" => "<PASSWORD>",
            "prefix" => "crr"
        ),
        "strava" => array(
            "clientId" => "<STRAVA_CLIENT_ID>",
            "clientSecret" => "<STRAVA_CLIENT_SECRET>"
        ),
        "runkeeper" => array(
            "clientId" => "<RUNKEEPER_CLIENT>",
            "clientSecret" => "<RUNKEEPER_CLIENT_SECRET>"
        )
    );

    public static function get($path) {
        $parts = explode("/", $path);
        $obj = self::$config;

        foreach ($parts as $part) {
            if(!isset($obj[$part])) {
                return null;
            }
            $obj = $obj[$part];
        }
        return $obj;


    }
}