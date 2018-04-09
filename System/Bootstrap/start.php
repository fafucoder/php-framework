<?php

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

//应用启动
\System\App::run();

$view = \System\View::instance();
// var_dump($view->twig);
$view->assign(array('name'=>'linrc'));
echo $view->display('index/index.html',array('site'=>'success'));
