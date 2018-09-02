<?php
$autoload = dirname(__DIR__) . '/system/framework/Core/Loader.php';
include_once $autoload;

$Loader = new Framework\Core\Loader(dirname(__DIR__));
$Loader->registerNamespaces(array(
	'Framework' => 'system/framework',
	'App' => 'application',
));
$Loader->registerExtends(array(
	'extends' => 'extends'
));
$Loader->registerAutoload(array(
	'vendor/autoload.php',
));

$Loader->register();
