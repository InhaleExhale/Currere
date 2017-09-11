<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:50
 */
session_start();

define("__ROOT", dirname(__FILE__));
define("__VENDOR", join(DIRECTORY_SEPARATOR, array(__ROOT, "vendor")));

require_once('./config.php');

/// TODO: Helper loader - this is rapidly getting excessive
require_once('./Helpers/Path.php');
require_once('./Helpers/Token.php');
require_once('./Helpers/Request.php');

require_once('connectors/Connector.php');

$connectors = Connectors\Factory::requireAll(true);
var_dump($connectors);

Connectors\Strava::test();

$action = \Helpers\Request::get('action', 'home');


