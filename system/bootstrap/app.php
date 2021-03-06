<?php
ob_start();

//引入常量配置（必须最先加载常量，相当于入口配置）
require "constant.php" ;

//加载自动加载类
require CORE_PATH . 'Loader.php';

//运行自动加载类
\System\Loader::register();

//错误和异常处理
\System\Error::register();

//加载composer
$autoload = ROOT_PATH . 'vendor/autoload.php';
if (file_exists($autoload)) {
	require ROOT_PATH . 'vendor/autoload.php';
}

// echo "hello world";exit;
//应用启动
\System\App::run()->send();
