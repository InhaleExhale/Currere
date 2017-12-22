<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 01/10/2017
 * Time: 15:46
 */

namespace Database;

function table($table)
{
    return \Config::get('database/prefix') . "_{$table}";
}

class Factory
{
    static private $_instance = null;

    static public function getInstance()
    {
        if (!self::$_instance) {
            try {
                $host = \Config::get('database/host');
                $dbname = \Config::get('database/name');
                $user = \Config::get('database/username');
                $pass = \Config::get('database/password');
                self::$_instance = new \PDO("mysql:host={$host};dbname={$dbname}", $user, $pass, array(
                    \PDO::ATTR_PERSISTENT => true
                ));

                self::$_instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        return self::$_instance;
    }
}