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

    public function __construct($params) {
        parent::__construct();

        $connectorName = \Helpers\Request::get('connector', null);
        $this->connector = \Connectors\Factory::create($connectorName);
        $this->connector->authorise();
    }

    public function getActivities($params) {
        $activities = $this->connector->getActivities($params);
        foreach($activities as $activity) {
            $activity->save();
        }

        echo "<pre>" . print_r($activities,1 ) . "</pre>";
    }


    public function defaultAction($params)
    {
        echo "DEFAULT AUTHENTICATION ACTION";
        // TODO: Implement defaultAction() method.
    }
}