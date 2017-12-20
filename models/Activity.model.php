<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 01/10/2017
 * Time: 15:39
 */

namespace Models;

class Activity extends Model
{

    const TABLE_NAME = 'activity';

    private $source;
    private $source_id;
    private $source_user;
    private $name;
    private $type;
    private $activity_date;

    private $total_distance;
    private $total_duration;
    private $average_speed;
    private $average_heartrate;
    private $elevation_gain;
    private $calories;

    private $route;
    private $start_position;

    private $source_object;

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

    public function bind($data) {
        parent::bind($data);
        if(is_string($this->source_object)) {
            $this->source_object = json_decode($this->source_object);
        }
    }

    public function set($field, $value) {
        $this->$field = $value;
    }

    public function get($field) {
        return $this->$field;
    }

    public function getSourceId()
    {
        return $this->source_id;
    }

    protected function fieldTemplate($item)
    {
        return $item->getName();
    }

    protected function propertyTemplate($item)
    {
        if ($item === "start_position") {
            return "POINT(:start_lat, :start_lng)";
        }
        return ":" . $item;
    }


    protected function bindFieldParams($stmt, $fields)
    {
        foreach ($fields as $field) {
            if ($field === 'start_position') {
                $stmt->bindParam(":start_lat", $this->start_position[0]);
                $stmt->bindParam(":start_lng", $this->start_position[1]);
                continue;
            } elseif ($field === "source_object") {
                $stmt->bindParam(":{$field}", json_encode($this->source_object));
            } else {
                $stmt->bindParam(":{$field}", $this->$field);
            }
        }
    }

    public function delete($id)
    {
    }

    static public function fromMapping($source, $mapping, $activity)
    {
        $data = parent::fromMapping($source, $mapping, $activity);

        return new Activity($data);
    }

    static public function fromRemote($sourceId, $connector)
    {
        $cache = \Helpers\Cache\CacheFactory::get("Activity");
        if (!$cache->check($sourceId)) {
            $remoteActivity = $connector->getActivity($sourceId);
            $cache->write($sourceId, $remoteActivity);
        }

        return $cache->read($sourceId);
    }

    static public function getLastUpdateTime()
    {
        $db = \Database\Factory::getInstance();
        $activityTable = \Database\table('activity');

        $sql = "SELECT `synced_date` FROM `{$activityTable}` ORDER BY `synced_date` DESC LIMIT 1";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $stringLastUpdate = $stmt->fetch();
            $timestampLastUpdate = $stringLastUpdate ? strtotime($stringLastUpdate[0]) : 0;
            return $timestampLastUpdate;
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    static public function getActivityRowsWithoutGear($type = null, $ignoreIndoor = false)
    {
        $db = \Database\Factory::getInstance();
        $activityTable = \Database\table('activity');
        $xrefTable = \Database\table('activity_gear_xref');


        $sql = "SELECT activity.id, activity.source_id FROM `{$activityTable}` AS activity
                LEFT JOIN `{$xrefTable}` AS gear_xref
                ON activity.id = gear_xref.activity_id";

        $wheres = array();
        if ($type) {
            $wheres[] = "activity.type='$type'";
        }

        if ($ignoreIndoor) {
            $wheres[] = "activity.route IS NOT NULL";
        }

        if (count($wheres) > 0) {
            $sql .= " WHERE " . join(" AND ", $wheres);
        }

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    static public function assignGear($activityId, $gearId)
    {
        $db = \Database\Factory::getInstance();
        $xrefTable = \Database\table('activity_gear_xref');

        $sql = "INSERT IGNORE INTO `{$xrefTable}` (activity_id, gear_id) VALUES(:activity_id, :gear_id)";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam("activity_id", $activityId);
            $stmt->bindParam("gear_id", $gearId);
            $stmt->execute();
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

}