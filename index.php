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





// include CORE.'/common/function.php';
// include CORE.'/system/Base.php';
// spl_autoload_register('\core\system\Base::autoload');
// \core\system\Base::run();

define('APP_PATH', __DIR__ . '/Application/');


require __DIR__.'/System/Bootstrap/start.php';
?>
