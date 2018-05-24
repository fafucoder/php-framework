<?php
namespace System;

/**
 * @todo
 */
class Cookie {

	/**
	 * 设置cookie选项
	 * @var array
	 */
	public static $option = [
		'expire' => 0,
		'path' => '/',
		'domain' => '/',
		'secure' => '',
	];

	/**
	 * 初始化
	 * @var bool
	 */
	protected static $init;

	/**
	 * 初始化cookie
	 * @param  array  $option 初始化选项
	 * @return void
	 */
	public static function init(array $option = array()) {
		if (empty($option)) {
			$option = Config::get('cookie');
		}
		self::$option = array_merge(self::$option, array_change_key_case($option));
	}

	/**
	 * 设置cookie
	 * @param string $name    cookie name
	 * @param string $value   cookie value
	 * @param array  $options cookie options
	 */
	public static function set($name, $value = '', $options = array()) {
		self::init();

		if (!empty($options)) {
			$option = array_merge(self::$option, $options);
		} else {
			$option = self::$option;
		}
		$expire = !empty($option['expire']) ?
        $_SERVER['REQUEST_TIME'] + intval($option['expire']) :
        0;

		if($option['setcookie']) {
			setCookie($name, $value, $expire, $option['path'], $option['domain'], $option['secure']);
		}
		$_COOKIE[$name] = $value;
	}

	/**
	 * 获取cookie
	 * @param  string $name cookie name
	 * @return mixed
	 */
	public static function get($name = '') {
		!isset(self::$init) && self::init();

		if ('' == $name) {
			return $_COOKIE;
		} elseif (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		} else {
			return null;
		}
	}

	/**
	 * 是否有cookie name
	 * @param  [string $name cookie name
	 * @return boolean
	 */
	public static function has($name) {
		!isset(self::$init) && self::init();
		return isset($_COOKIE[$name]);
	}

	/**
	 * 删除cookie
	 * @param  string $name cookie name
	 * @return void
	 */
	public static function delete($name = '') {
		!isset(self::$init) && self::init();
		if ($option['setcookie']) {
			setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $option['path'],$option['domain'], $option['secure']
			);
		}
		unset($_COOKIE[$name]);
	}

}
