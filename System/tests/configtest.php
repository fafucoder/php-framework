<?php

//引入常量配置（必须最先加载常量，相当于入口配置）
require "../Bootstrap/constant.php" ;

//加载自动加载类
require CORE_PATH . 'Loader.php';

//运行自动加载类
$Loader = new \System\Loader();
$Loader->register();

//加载composer
$autolod = ROOT_PATH . 'vendor/autoload.php';
if (file_exists($autoloa)) {
	require ROOT_PATH . 'vendor/autoload.php';
}

// 启动应用
\System\App::run();

$path = array(
	'APP_CONF' => APP_PATH.'Conf/',
	'SYS_CONF' => CORE_PATH."Conf/"
);
$config1 = array(
	'db'=> array (
		'user'=>'linrc',
		'passwd'=>'toor',
	),
);
$config2 = array(
	'app_path'=>'application'
);
$config3 = array(
	'app_path1'=>'application2'
);
$config = '\\System\\Config';
$config::setConfigPath($path);
var_dump($config::getConfigPath());
var_dump($config::set($config2));
var_dump($config::set($config1));
var_dump($config::get('user'));
var_dump($config::get('db.user'));

var_dump($config::load('route'));
// var_dump($config::load('config'));
new \System\Drivers\ParseConf\Ini();