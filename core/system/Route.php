<?php

namespace core\system;

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
			$path = explode($_SERVER['SCRIPT_NAME'],$_SERVER['REQUEST_URI'])[1];
			$patharr = explode("/", trim($path,"/"));
			if (isset($patharr[0]) && !empty($patharr[0])) {
				$this->controller = $patharr[0];
			}
			else
			{
				$this->controller="index";
			}
			if (isset($patharr[1]) && !empty($patharr[1])) {
				$this->action = $patharr[1];
			}
			else
			{
				$this->action = "index";
			}
		}
		else
		{
			$this->controller="index";
			$this->action="index";
		}
		echo $this->controller;
		echo $this->action;
	}

}