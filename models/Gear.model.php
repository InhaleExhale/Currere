<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 01/10/2017
 * Time: 15:39
 */

namespace Models;

class Gear extends Model
{
    const TABLE_NAME = 'gear';

    private $source;
    private $source_id;
    private $name;
    private $description;
    private $source_object;

    public function __construct($data = null)
    {
        parent::__construct($data);
    }

    public function bind($data)
    {
        parent::bind($data);
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

    public function delete($id)
    {
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
            if ($field === "source_object") {
                $stmt->bindParam(":{$field}", json_encode($this->source_object));
            } else {
                $stmt->bindParam(":{$field}", $this->$field);
            }
        }
    }

    public static function fromMapping($source, $mapping, $gear)
    {
        $data = parent::fromMapping($source, $mapping, $gear);

        return new Gear($data);
    }

    // TODO This is not the place for this, but I need somewhere to store the query so I can go to the pub.
    public static function gearDistances($outdoorRunsOnly = true)
    {
        $db = \Database\Factory::getInstance();
        $activityTable = \Database\table('activity');
        $xrefTable = \Database\table('activity_gear_xref');
        $gearTable = \Database\table('gear');

        $where = $outdoorRunsOnly ? "WHERE a.type='RUN' AND a.route IS NOT NULL" : "";

        $sql = "SELECT g.id, g.name, COUNT(a.id) AS 'num_activities', SUM(a.total_distance) AS distance_metres
                FROM `{$xrefTable}` as x 
                INNER JOIN {$activityTable} AS a 
                ON a.id = x.activity_id
                INNER JOIN {$gearTable} AS g
                ON g.id = x.gear_id
                $where
                GROUP BY g.id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $data;

    }


}