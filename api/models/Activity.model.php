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

    public function bind($data)
    {
        parent::bind($data);
        if(!is_array($this->start_position) && strlen($this->start_position) > 0) {
            $coordinates = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $this->start_position);
            $this->start_position = array($coordinates['lat'], $coordinates['lon']);
        }

        if (is_string($this->source_object)) {
            $this->source_object = json_decode($this->source_object);
        }
    }

    public function set($field, $value)
    {
        $this->$field = $value;
    }

    public function get($field)
    {
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

    public static function fromMapping($source, $mapping, $activity)
    {
        $data = parent::fromMapping($source, $mapping, $activity);

        return new Activity($data);
    }

    public static function fromRemote($sourceId, $connector)
    {
        $cache = \Helpers\Cache\CacheFactory::get("Activity");
        if (!$cache->check($sourceId)) {
            $remoteActivity = $connector->getActivity($sourceId);
            $cache->write($sourceId, $remoteActivity);
        }

        return $cache->read($sourceId);
    }

    public static function getLastUpdateTime()
    {
        $db = \Database\Factory::getInstance();
        $activityTable = \Database\table('activity');

        $sql = "SELECT `synced_date` FROM `{$activityTable}` ORDER BY `synced_date` DESC LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stringLastUpdate = $stmt->fetch();
        $timestampLastUpdate = $stringLastUpdate ? strtotime($stringLastUpdate[0]) : 0;
        return $timestampLastUpdate;
    }

    public static function getActivityRowsWithoutGear($type = null, $ignoreIndoor = false)
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

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getList($type = null, $ignoreIndoor = true, $page = 0, $perPage = 500)
    {
        $db = \Database\Factory::getInstance();
        $activityTable = \Database\table('activity');

        $startingOffset = $page * $perPage;

        $wheres = self::sqlFilters($type, $ignoreIndoor);

        $sql = "SELECT activity.* FROM {$activityTable} AS activity";

        if (count($wheres) > 0) {
            $sql .= " WHERE " . join(" AND ", $wheres);
        }
        $sql .= " ORDER BY activity.activity_date ASC  LIMIT {$startingOffset}, $perPage";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return new Activity($item);
        }, $items);
    }

    public static function assignGear($activityId, $gearId)
    {
        $db = \Database\Factory::getInstance();
        $xrefTable = \Database\table('activity_gear_xref');

        $sql = "INSERT IGNORE INTO `{$xrefTable}` (activity_id, gear_id) VALUES(:activity_id, :gear_id)";

        $stmt = $db->prepare($sql);
        $stmt->bindParam("activity_id", $activityId);
        $stmt->bindParam("gear_id", $gearId);
        $stmt->execute();
    }

    private static function sqlFilters($type = null, $ignoreIndoor = true, $wheres = array())
    {
        if ($type) {
            $wheres[] = "activity.type='$type'";
        }

        if ($ignoreIndoor) {
            $wheres[] = "activity.route IS NOT NULL";
        }

        return $wheres;
    }

}