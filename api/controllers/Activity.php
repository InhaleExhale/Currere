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
    }

    public function activities($params)
    {
        $response = array(
            "status" => false
        );

        $page = self::param('page', 0);
        $perPage = self::param('per_page', 500);
        $type = self::param('type', null);
        $ignoreIndoor = self::param('ignoreIndoor', false);

        try {
            $activities = \Models\Activity::getList($type, $ignoreIndoor, $page, $perPage);
            $response["status"] = true;
            $response["activities"] = array_map(function ($activity) {
                return $activity->raw();
            }, $activities);
        } catch (\PDOException $e) {
            $response["message"] = "Failed to query database. Check logs for more details.";
            self::error($e);
        }

        $this->done($response);
    }

    public function get($params)
    {
        $message = "";
        $response = array(
            "status" => false
        );

        if (count($this->requestParams) < 1) {
            $message = "Activity ID not provided";
        } else {
            try {
                $id = $this->requestParams[0];
                $activity = new \Models\Activity();
                if ($activity->load($id)) {
                    $response["status"] = true;
                    $response["activity"] = $activity->raw();
                } else {
                    $message = "Activity with id: $id was not found";
                }
            } catch (\PDOException $e) {
                $message = "Failed to query database. Check logs for more details.";
                self::error($e);
            }
        }

        if (!$response["status"]) {
            $response["message"] = $message;
        }

        $this->done($response);
    }


    public function defaultAction($params)
    {
        echo "DEFAULT AUTHENTICATION ACTION";
        // TODO: Implement defaultAction() method.
    }
}