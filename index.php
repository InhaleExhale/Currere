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

require_once('vendor/autoload.php');
require_once('./config.php');

/// TODO: Helper loader - this is rapidly getting excessive
require_once('./Helper.php');
Helpers\Helper::loadAll();

// Template
Helpers\Templates::get();

require_once('connectors/Connector.php');
$connectors = Connectors\Factory::requireAll(true);

$controllerName = \Helpers\Request::get('controller', 'Core');
$actionName = \Helpers\Request::get('action', 'defaultAction');

require_once(Helpers\Path::join(array(__ROOT, 'Controllers', 'Base.php')));
require_once(Helpers\Path::join(array(__ROOT, 'Controllers', $controllerName . '.php')));
$controllerClassName = "\\Controllers\\{$controllerName}";
$controller = new $controllerClassName(array(/* TODO: probably something needs to go here */));
$controller->dispatch($actionName);


