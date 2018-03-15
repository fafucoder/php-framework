<?php
namespace core\system;
use core\system\Config;
class Route {
	public $controller;
	public $action;
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
