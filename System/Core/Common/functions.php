<?php

if (!function_exists('config')) {
	/**
	 * config配置或者获取
	 * @param  string|array $name  config name
	 * @param  mixed $value config value
	 * @return mixed
	 */
	function config($name = "", $value = null) {
		if (!is_null($value)) {
			return \System\Config::set($name, $value);
		}
		return \System\Config::get($name);
	}
}

if (!function_exists('php_version')) {
	/**
	 * php版本比较
	 * @param  string  $v      version
	 * @param  string $oprate operate
	 * @return boolean
	 */
	function php_version($v= null,$oprate = '>=') {
		static $_version;
		if (is_null($v) && !isset($_version)) {
			return PHP_VERSION;
		}
		return version_compare(PHP_VERSION, $v ,$oprate);
	}
}

if(!function_exists('is_https')) {
	/**
	 * 检测是否是http
	 * @return boolean
	 */
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

if (!function_exists('base_url')) {
	/**
	 * 网站的baseurl
	 * @return string
	 */
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
	/**
	 * 加载配置
	 * @param  string $file file name
	 * @param  string $type file type
	 * @return mixed
	 */
	function load_config($file, $type = "") {
		$files = CONF_PATH . pathinfo($file, PATHINFO_FILENAME) . !empty($type) ? $type : EXT;
		if (file_exists($files)) {
			return include($files);
		}
	}
}

if (!function_exists('input')) {
	/**
	 * 获取输入变量
	 * @param  string $key     name
	 * @param  mixed  $default default
	 * @param  string $filter  filter
	 * @return mixed
	 */
	function input($key, $default = null, $filter = '') {

	}
}

if (!function_exists('model')) {
	/**
	 * 加载model
	 * @param  string $name [description]
	 * @return [type]       [description]
	 */
	function model($name = '') {

	}
}

if (!function_exists('url')) {
	/**
	 * 生成url
	 * @param  string $url  url info
	 * @param  string $vars vars
	 * @return string
	 */
	function url($url = '', $vars = '') {

	}
}

if (!function_exists('session')) {
	/**
	 * 设置session
	 * @param  string  $name  session name
	 * @param  string $value session value
	 * @return mixed
	 */
	function session($name, $value = '') {

	}
}

if (!function_exists('cookie')) {
	/**
	 * 设置cookie
	 * @param  string  $name  cookie name
	 * @param  string  $value cokie value
	 * @return mixed
	 */
	function cookie($name, $value = '') {

	}
}

if (!function_exists('request')) {
	/**
	 * request生成
	 * @return Request
	 */
	function request() {
		return \System\Request::instance();
	}
}

if (!function_exists('response')) {
	function response() {

	}
}
