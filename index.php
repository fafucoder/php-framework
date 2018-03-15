<?php

/**
 * 入口文件
 * 1.定义常量
 * 加载函数库
 * 启动框架
 */
// define ("MINI",realpath('./'));
// define("CORE", MINI."/core");
// define("APP",MINI."/application");

// define('DEBUG', true);

// // composer加载
// require "vendor/autoload.php";

// if (DEBUG) {
// 	$whoops = new \Whoops\Run;
// 	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
// 	$whoops->register();
// 	ini_set('display_errors', "On");
// }else {
// 	ini_set('display_errors','Off');
// }

// include CORE.'/common/function.php';
// include CORE.'/system/Base.php';
// spl_autoload_register('\core\system\Base::autoload');
// \core\system\Base::run();

define('APP_PATH', __DIR__ . '/../application/');

require __DIR__.'/system/bootstrap/start.php';
?>