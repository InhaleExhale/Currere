<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 12/09/2017
 * Time: 21:57
 */

namespace Controllers;


class Activity extends Base
{
    private $connector;
    private $task;

    public function __construct($params)
    {
        parent::__construct();

        $connectorName = \Helpers\Request::get('connector', null);
        $this->connector = \Connectors\Factory::create($connectorName);
        $this->connector->authorise();
    }

    public function getAllActivities($params)
    {
        if (isset($_REQUEST['newOnly'])) {
            $params['since'] = \Models\Activity::getLastUpdateTime();
        }
        $activities = $this->connector->getAllActivities($params);

        foreach ($activities as $activity) {
            $activity->save();
        }

        $numItems = count($activities);
        echo "Loaded $numItems items into the Database.";
    }

    // TODO This method is an abomination and needs to be severely thinned down...
    public function getGearForActivities($params)
    {
        set_time_limit(0);

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


        return Array(
            "uniqueGear" => $uniqueGearItems,
            "gearActivityLUT" => $gearToActivityIds,
            "missingGear" => $missingGear
        );
    }


    public function defaultAction($params)
    {
        echo "DEFAULT AUTHENTICATION ACTION";
        // TODO: Implement defaultAction() method.
    }

    public function uncache($params)
    {
        if (!isset($_REQUEST['activityId'])) {
            header('Location: http://localhost/Currere');
            exit();
        }
        $activityId = $_REQUEST['activityId'];

        $cache = \Helpers\Cache\CacheFactory::get('Activity');
        if (!$cache->delete($activityId)) {
            die('Could not delete item from cache');
        } else {
            die('Success');
        }
    }
}