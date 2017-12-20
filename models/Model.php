<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 01/10/2017
 * Time: 15:40
 */

namespace Models;


abstract class Model
{
    protected $id;
    protected $_db;

    function __construct($data)
    {
        $this->_db = \Database\Factory::getInstance();

        if (is_array($data)) {
            $this->bind($data);
        } else {
            $this->id = $data;
        }
    }


    public abstract function set($field, $value);
    public abstract function get($field);

    public function bind($data)
    {
        foreach ($data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->set($field, $value);
            }
        }
    }

    public function load($id, $idField='id')
    {
        // We can either use source_id or id, nothing else. Bear in mind, we make no guarantees of the individuality of
        // source_id and simply take the first one that appears.
        $idField = $idField == 'source_id' ? 'source_id' : 'id';
        $dbTable = \Database\table(static::TABLE_NAME);
        $sql = "SELECT * FROM {$dbTable} WHERE {$idField} = :id LIMIT 1";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($data === false) {
            return false;
        }

        $this->bind($data);
        return true;
    }

    public function save()
    {
        if ($this->id === null) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    public function insert()
    {
        $dbTable = \Database\table(static::TABLE_NAME);

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
        $dbTable = \Database\table(static::TABLE_NAME);

        $fieldProps = $this->getFieldsAndProperties('update');

        $id = $this->get('id');
        $sql = "UPDATE {$dbTable} SET {$fieldProps->updateString} WHERE id={$id};";

        try {
            $stmt = $this->_db->prepare($sql);
            $this->bindFieldParams($stmt, $fieldProps->fields);
            $stmt->execute();
        } catch (\PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    abstract public function delete($id);

    /*
     * Sub-classes must implement a mechanism to extract the fields and SQL statement properties from the object.
     * These are likely to differ from class to class, so have purposely not been implemented here.
     *
     * fieldTemplate should define how to interpret the results of the Reflection call (with IS_PRIVATE filter).
     * propertyTemplate should convert field names to PDO statement placeholders
     * bindFieldParams should perform the actual binding of instance vars onto those placeholders.
     */
    abstract protected function fieldTemplate($item);

    abstract protected function propertyTemplate($item);

    abstract protected function bindFieldParams($stmt, $fields);

    protected function getFieldsAndProperties($mode = 'INSERT')
    {
        $reflection = new \ReflectionClass($this);
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        $output = new \StdClass();

        // Extract field names from Reflection object
        $output->fields = array_map(array($this, 'fieldTemplate'), $vars);

        // Extract string template placeholders for SQL statements, formatting POINT's as geo objects
        $output->properties = array_map(array($this, 'propertyTemplate'), $output->fields);

        if ($mode == 'INSERT') {
            // Convert to SQL INSERT strings
            $output->fieldString = implode(', ', $output->fields);
            $output->propertyString = implode(', ', $output->properties);
        } else {
            $combinedFieldsAndProps = array_map(function ($field, $prop) {
                return "$field = $prop";
            }, $output->fields, $output->properties);

            $output->updateString = join(", ", $combinedFieldsAndProps);
        }
        return $output;
    }

    static public function fromSourceId($sourceId) {
        $className = get_called_class();
        $obj = new $className();
        $result = $obj->load($sourceId, 'source_id');
        return $result ? $obj : false;
    }

    public static function fromMapping($source, $mapping, $object)
    {
        $data = array(
            "id" => null,
            "source" => $source,
            "source_object" => $object
        );

        /// TODO: If field doesn't exist, just map it to ""/
        // 3 syntaxes supported, raw field value, child field (using '->', any depth) or an array of fields to be added
        foreach ($mapping as $activityField => $mappingField) {
            if (is_array($mappingField)) {
                $data[$activityField] = array_map(function ($item) use ($object) {
                    return $object->$item;
                }, $mappingField);
            } elseif (strrpos($mappingField, '->') !== false) {
                $mappingFields = explode('->', $mappingField);
                $currentValue = $object;
                foreach ($mappingFields as $subField) {
                    $currentValue = $currentValue->$subField;
                }
                $data[$activityField] = $currentValue;
            } elseif ($mappingField === "") {
                $data[$activityField] = null;
            } else {
                $data[$activityField] = $object->$mappingField;
            }
        }

        return $data;
    }
}

class Factory
{
    static $models = array();

    static function getList()
    {
        return self::$models;
    }

    static function requireAll($load = true)
    {
        $modelRoot = \Helpers\Path::join(array(__ROOT, "models"));

        $dirs = array();
        foreach (new \DirectoryIterator($modelRoot) as $file) {
            if ($file->isFile() && !$file->isDot() && self::isModel($file->getBasename('.php'))) {
                $filename = $file->getFilename();
                if ($load) {
                    $modelPath = \Helpers\Path::join(array($modelRoot, $filename));
                    self::$models[] = $file->getBasename('.model.php');
                    require_once($modelPath);
                }
            }
        }
        return $dirs;
    }

    static function isModel($filename)
    {
        $pieces = explode('.', $filename);
        return array_pop($pieces) == 'model';
    }

    static function create($type, $options = array())
    {
        if (!in_array($type, self::$models)) {
            throw new \Exception("Model not found: " . $type);
        }
        $type = "\\Connectors\\{$type}";
        return $type::create($options);
    }
}