<?php
namespace System;

class Routes {
	/**
	 * 控制器
	 * @var string
	 */
	public static $controller;

	/**
	 * 方法
	 * @var [type]
	 */
	public static $action;

	/**
	 * 配置信息
	 * @var [type]
	 */
	public static $config = array();

	/**
	 * 路由
	 */
	public static $routes = array();

	/**
	 * 路由类型
	 */
	public static $type ;

	/**
	 * uri
	 */
	public $uri;


	public function __construct() {
		self::$routes = array_merge(self::$routes, Config::load('route'));
		self::$config = array_merge(self::$config, Config::get('url');
		$this->uri = new Uri();


	}

	public function init() {
		self::$routes = array_merge(self::$routes, Config::load('route'));
		self::$config = array_merge(self::$config, Config::get('url');
		
	}
	/**
	* 隐藏对应的index.php
	* 获取url，参数部分
	* 返回对应控制器和方法
	*/
	public function __construct()
	{
		if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/') {
			// 获取请求地址
			$root = $_SERVER['SCRIPT_NAME'];  
			$request = $_SERVER['REQUEST_URI'];
			$url = str_replace($root, "", $request);
			$patharr = explode("/",trim($url,'/'));
			if (isset($patharr[0]) && !empty($patharr[0])) {
				$this->controller = $patharr[0];
				unset($patharr[0]);
			} else {
				$this->controller="index";

			}
			if (isset($patharr[1]) && !empty($patharr[1])) {
				$this->action = $patharr[1];
				unset($patharr[1]);
			} else {
				$this->action = "index";
			}
			$count = count($patharr);
			$i=2;
			while ($i<$count+2) {
				if (isset($patharr[$i+1])) {
					$_GET[$patharr[$i]] = $patharr[$i+1];
				}
				$i+=2;
			}
		}
		else
		{
			$this->controller=Config::get('CONTROLLER',"route");
			$this->action=Config::get('ACTION',"route");
		}
	}

}
