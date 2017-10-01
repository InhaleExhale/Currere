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
        parent::__construct();

        if (is_array($data)) {
            $this->bind($data);
        } else {
            $this->id = $data;
        }
    }

    public function bind($data)
    {
        foreach ($data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
    }


    public function save()
    {
        if ($this->id === null) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function getFieldsAndProperties()
    {
        $reflection = new \ReflectionClass($this);
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        $output = new \StdClass();
        $output->fields = array_map(function ($item) {
            return $item->getName();
        }, $vars);
        $output->fieldString = implode(', ', $output->fields);

        $output->properties = array_map(function ($item) {
            if ($item === "start_position") {
                return "POINT(:start_lat, :start_lng)";
            }
            return ":" . $item;
        }, $output->fields);

        $output->propertyString = implode(', ', $output->properties);
        return $output;
    }

    private function bindFieldParams($stmt, $fields)
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

    public function insert()
    {
        $dbTable = \Database\table('activity');

        $fieldProps = $this->getFieldsAndProperties();

        $sql = "INSERT INTO {$dbTable} ({$fieldProps->fieldString}) VALUES ({$fieldProps->propertyString});";

        try {
            $stmt = $this->_db->prepare($sql);
            $this->bindFieldParams($stmt, $fieldProps->fields);
            $stmt->execute();
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function update()
    {

    }

    public function load($id)
    {
    }

    public function delete($id)
    {
    }


    static public function fromMapping($source, $mapping, $activity)
    {
        $data = array(
            "id" => null,
            "source" => $source,
            "source_object" => $activity
        );

        /// TODO: If field doesn't exist, just map it to ""/
        // 3 syntaxes supported, raw field value, child field (using '->', any depth) or an array of fields to be added
        foreach ($mapping as $activityField => $mappingField) {
            if (is_array($mappingField)) {
                $data[$activityField] = array_map(function ($item) use ($activity) {
                    return $activity->$item;
                }, $mappingField);
            } elseif (strrpos($mappingField, '->') !== false) {
                $mappingFields = explode('->', $mappingField);
                $currentValue = $activity;
                foreach ($mappingFields as $subField) {
                    $currentValue = $currentValue->$subField;
                }
                $data[$activityField] = $currentValue;
            } elseif ($mappingField === "") {
                $data[$activityField] = null;
            } else {
                $data[$activityField] = $activity->$mappingField;
            }
        }

        return new Activity($data);
    }

}