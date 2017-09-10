<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 10/09/2017
 * Time: 21:50
 */

define("__ROOT", dirname(__FILE__));
define("__VENDOR", join(DIRECTORY_SEPARATOR, array(__ROOT, "vendor")));

require_once('./config.php');
require_once('./Helpers/Path.php');

require_once('connectors/Connector.php');

$connectors = Connectors\Factory::all(true);
var_dump($connectors);

Connectors\Strava::test();

