<?php

//引入常量配置（必须最先加载常量，相当于入口配置）
require "constant.php" ;

//加载自动加载类
require CORE_PATH . 'Loader.php';

//运行自动加载类
$Loader = new \System\Loader();
$Loader->register();

//加载composer
$autoload = ROOT_PATH . 'vendor/autoload.php';
if (file_exists($autoload)) {
	require ROOT_PATH . 'vendor/autoload.php';
}

// 启动应用
// \System\App::run();
$path = array(
	'APP_CONF' => APP_PATH.'Conf/',
	'SYS_CONF' => CORE_PATH."Conf/"
);
\System\Config::setConfigPath($path);
\System\Config::load('default');

$uri = new \System\URI();

var_dump($uri->actions);

var_dump($uri->arguments);

