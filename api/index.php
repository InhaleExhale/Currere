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

ob_start();
require_once('vendor/autoload.php');
require_once('./config.php');

require_once('./core/Database.php');

require_once('./Helper.php');
Helpers\Helper::loadAll();
Helpers\Request::setController();

// Template
Helpers\Templates::get();

// Load base classes
require_once(Helpers\Path::join(array('connectors', 'Connector.php')));
require_once(Helpers\Path::join(array('models', 'Model.php')));
require_once(Helpers\Path::join(array('controllers', 'Base.php')));

$connectors = Connectors\Factory::requireAll(true);
$models = Models\Factory::requireAll(true);

$actionName = \Helpers\Request::get('action', 'defaultAction');



$controllerName = \Helpers\Request::get('controller', 'Core');
require_once(Helpers\Path::join(array('controllers', $controllerName . '.php')));
$controllerClassName = "\\Controllers\\{$controllerName}";
$controller = new $controllerClassName(array(/* TODO: probably something needs to go here */));
$controller->dispatch($actionName);

echo ob_get_clean();
