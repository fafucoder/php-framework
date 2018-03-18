<?php

//引入常量配置（必须最先加载常量，相当于入口配置）
require "constant.php" ;

//加载自动加载类
require CORE_PATH . 'Loader.php';

//运行自动加载类
$Loader = new \System\Loader();
$Loader->register();

//加载composer
require ROOT_PATH . 'vendor/autoload.php';

//DEBUG配置
if (DEBUG) {
	$whoops = new \Whoops\Run();
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();
	ini_set('display_errors', "On");
}else {
	ini_set('display_errors','Off');
}
