<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:43
 */

namespace Connectors;

require_once(__ROOT . "/connectors/Connector.php");
require_once("Auth.php");
use Iamstuartwilson\StravaApi;
use Models\Activity;


class Strava extends Connector
{
    const name = "Strava Connector";
    const version = "0.0.1";
    const MAX_REQUESTS = 500;


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

    public function getAllActivities($params = Array())
    {
        // TODO: Implement getActivities() method.
        $page = 1;
        $allActivities = Array();

        $since = isset($params['since']) ? $params['since'] : 0;

        while(true) {
            $activities = $this->api->get('athlete/activities', [
                'per_page' => 100,
                'page' => $page++,
                'after' => $since
            ]);

            if (count($activities) == 0) {
               break;
            }
            $allActivities = array_merge($allActivities, $activities);
        }

        return array_map('self::activity', $allActivities);
    }

    public function getActivity($id, $params = Array()) {
        return $this->api->get("activities/$id");
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

    static function activity($activity) {

        $mapping = array(
            "source_id" => "id",
            "source_user" => "athlete->id",
            "name" => "name",
            "type" => "type",
            "activity_date" => "start_date",
            "total_distance" => "distance",
            "total_duration" => "elapsed_time",
            "average_speed" => "average_speed",
            "average_heartrate" => $activity->has_heartrate ? "average_heartrate" : "",
            "elevation_gain" => "total_elevation_gain",
            "calories" => "",
            "route" => "map->summary_polyline",
            "start_position" => array("start_latitude", "start_longitude")
        );

        return \Models\Activity::fromMapping('Strava', $mapping, $activity);
    }

    static function gear($gear) {

        $mapping = array(
            "source_id" => "id",
            "name" => "name",
            "description" => "",
        );

        return \Models\Gear::fromMapping('Strava', $mapping, $gear);
    }

}