<?php

/**
 * 入口文件
 * 1.定义常量
 * 加载函数库
 * 启动框架
 */
define ("MINI",realpath('./'));
define("CORE", MINI."/core");
define("APP",MINI."/app");

define('DEBUG', true);

if (DEBUG) {
	// ini_set('display_error', "On");
}else {
	// ini_set('display_error','Off');
}

include CORE.'/common/function.php';

include CORE.'/system/base.php';
spl_autoload_register('\core\system\Base::autoload');
\core\system\Base::run();

?>