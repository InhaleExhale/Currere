<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 22/12/2017
 * Time: 13:35
 */

namespace Controllers;

class Connector extends Base
{
    private $connector;
    private $task;

    public function __construct($params)
    {
        parent::__construct();

        $connectorName = ucwords(count($this->requestParams) > 0
            ? $this->requestParams[0] : \Config::get('core/defaultConnector'));

        if ($connectorName != null) {
            try {
                $this->connector = \Connectors\Factory::create($connectorName);
                $this->connector->authorise();
            } catch(\Exception $e) {
                self::error($e);
                $this->done(array(
                    "status" => false,
                    "message" => "Could not find connector {$connectorName}."
                ));
            }
        }
    }

    public function listAll($params)
    {
        $connectors = \Connectors\Factory::createAll();

        $connectorOutput = array_map(function ($connector) {
            $name = $connector->getClass(true);
            $lowerCaseName = strtolower($name);
            return array(
                "name" => $name,
                "authorised" => $connector->isAuthorised(),
                "urls" => array(
                    "authorise" => $connector->authoriseLink(),
                    "disconnect" => "/authentication/deauthorise/{$lowerCaseName}",
                    "allActivities" => "/connector/activities/{$lowerCaseName}",
                    "newActivities" => "/connector/activities/{$lowerCaseName}/newOnly/true",
                    "gear" => "/connector/gear/{$lowerCaseName}",
                )
            );
        }, $connectors);

        $response = array(
            "status" => true,
            "connectors" => $connectorOutput
        );

        $this->done($response);
    }

    public function activities($params)
    {
        $response = array("success" => false);
        try {
            if ($this->param('newOnly', false)) {
                $params['since'] = \Models\Activity::getLastUpdateTime();
            }

            $activities = $this->connector->getAllActivities($params);

            foreach ($activities as $activity) {
                $activity->save();
            }

            $numItems = count($activities);
            $response["success"] = true;
            $response["message"] = "Loaded $numItems items into the Database.";
        } catch (\PDOException $e) {
            $response["message"] = "Failed to query database. Check logs for more details.";
            self::error($e);
        }

        $this->done($response);
    }

    // TODO This method is an abomination and needs to be severely thinned down...
    public function gear($params)
    {
        set_time_limit(0);
        $response = array("success" => false);

        try {
            $rowsToFetch = \Models\Activity::getActivityRowsWithoutGear();

            $uniqueGearItems = Array();
            $gearToActivityIds = Array();
            $missingGear = Array();

            foreach ($rowsToFetch as $row) {
                $remoteActivity = \Models\Activity::fromRemote($row['source_id'], $this->connector);

                $gearSourceId = $remoteActivity["gear_id"];
                if (!$gearSourceId) {
                    $missingGear[] = [
                        "id" => $remoteActivity["id"],
                        "name" => $remoteActivity["name"]
                    ];
                    continue;
                }

                if (!array_key_exists($gearSourceId, $uniqueGearItems)) {
                    $uniqueGearItems[$gearSourceId] = (object)$remoteActivity["gear"];
                    $gearToActivityIds[$gearSourceId] = Array();
                }

                $gearToActivityIds[$gearSourceId][] = $row["id"];
            }

            $connectorClass = "\\" . get_class($this->connector);
            $gearItems = array_map("$connectorClass::gear", $uniqueGearItems);

            foreach ($gearItems as $sourceId => $item) {
                $existingGear = new \Models\Gear();
                $exists = $existingGear->load($item->get('source_id'), 'source_id');

                // We take the remote as a source of truth, so overwrite our own data with anything updated at source.
                if ($exists) {
                    $item->set('id', $existingGear->get('id'));
                }

                $item->save();

                foreach ($gearToActivityIds[$sourceId] as $activityId) {
                    \Models\Activity::assignGear($activityId, $item->get('id'));
                }
            }

            $response["success"] = true;
        } catch (\PDOException $e) {
            $response["message"] = "Failed to query database. Check logs for more details.";
            self::error($e);
        }

        $this->done($response);

    }

    public function uncache($params)
    {
        $activityId = $this->param('activityId', false);
        if ($activityId === false) {
            $this->done(array(
                "success" => false,
                "message" => "activityId not set"
            ));
        }

        $cache = \Helpers\Cache\CacheFactory::get('Activity');
        if (!$cache->delete($activityId)) {
            $this->done(array(
                "success" => false,
                "message" => "Could not delete item from cache"
            ));
        } else {
            $this->done(array(
                "success" => true
            ));
        }
    }

    public function defaultAction($params)
    {
        $connectorNamespaced = $this->connector->getClass(true);
        $connectorName = explode("\\", $connectorNamespaced)[1];

        $this->done(array(
            "connector" => $connectorName,
            "status" => $this->connector->isAuthorised() ? "authorised" : "not authorised"
        ));
    }
}