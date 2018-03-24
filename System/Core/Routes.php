<?php 
namespace System;

class Routes {

	/**
	 * 控制器名
	 */
	public static $controller = "Index";

	/**
	 * 方法名
	 * @var 
	 */	
	public static $action = "index";

	/**
	 * 路由规则
	 */
	public static $rules = array();

	/**
	 * config配置
	 */
	public static $config = array();

	/**
	 * 空控制器
	 */
	public static $empty_controller = "Empty";

	/**
	 * 类后缀
	 * @var string
	 */
	public static $class_suffix = "";

	/**
	 * 控制器后缀
	 * @var string
	 */
	public static $controller_suffix = "";

	/**
	 * 方法前缀
	 * @var string
	 */
	public static $action_prefix = "";

	/**
	 * 方法后缀
	 * @var string
	 */
	public static $action_suffix = "";

	/**
	 * 返回类型
	 * @var string
	 */
	public static $return_type = "";

	/**
	 * 路由模式
	 * @var integer
	 */
	public static $url_mode = 1;


	public static function init() {
		self::$config = Config::get('app');
	}

	


}