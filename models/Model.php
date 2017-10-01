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
    private $_id;
    private $_db;

    function __construct()
    {
        $this->_db = \Database\Factory::getInstance();
    }

    abstract public function bind($data);
    abstract public function load($id);
    abstract public function save();
    abstract public function delete($id);
}