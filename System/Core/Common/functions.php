<?php

function p($var) {
	var_dump($var);
}

if (!function_exists('config')) {
	function config($name ="", $value = null) {
		if (!is_null($value)) {
			return \System\Config::set($name, $value);
		}
		return \System\Config::get($name);
	}
}

if (!function_exists('php_version')) {
	function php_version($v= null,$oprate = '>=') {
		static $_version;
		if (is_null($v) && !isset($_version)) {
			return PHP_VERSION;
		}
		return version_compare(PHP_VERSION, $v ,$oprate);
	}
}

//检查是否是https
if(!function_exists('is_https')) {
	function is_https() {
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
			return TRUE;
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
			return TRUE;
		} elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
			return TRUE;
		}
		return FALSE;
	}
}

//获取主页
if (!function_exists('base_url')) {
	function base_url() {
		$http = is_https() ? 'https://' : 'http://';
		$host = $_SERVER['HTTP_HOST'];
		$home = $_SERVER['SCRIPT_NAME'];
		return str_replace("\\", "/", $http . $host . $home);
	}
}

/**
 * 加载配置
 * @TODO 异常还没做
 */
if (!function_exists('load_config')) {
	function load_config($file, $type = "") {
		$files = CONF_PATH . pathinfo($file, PATHINFO_FILENAME) . (("" !== $type) ? $type : EXT);
		if (file_exists($files)) {
			return include($files);
		}
		//这是异常处理
		
	}
}