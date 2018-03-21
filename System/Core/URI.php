<?php 
namespace System;

class URI {
	
	/**
	 * 路由模式
	 * @var [type]
	 */
	public $url_mode = 1;

	/**
	 * 参数
	 */
	public $arguments = array();
	/**
	 * url
	 */
	public $url ;

	public $actions = array();

	public function __construct() {
		if (null !==Config::get('url.url_mode')) {
			$this->url_mode = Config::get('url.url_mode');
		}
		$this->url_mode=0;
		if (is_numeric($this->url_mode)) {
			switch ($this->url_mode) {
				case 1:
					$this->parse_path_info();
					break;
				default:
					// $this->parse_query_string();
					$this->_parse_query_string();
					break;
			}
		} else {
			// throw new Exception("路由模式错误");
			echo "路由模式错误";
		}
	}

	public function parse_path_info() {
		if (!isset($_SERVER['REQUEST_URI'],$_SERVER['SCRIPT_NAME'])) {
			return "";
		}
		$path_info = trim($_SERVER['PATH_INFO'],"/");
		$query_string = $_SERVER['QUERY_STRING'];
		$url = explode('/', $path_info,3);
		$this->actions['controller'] = ucfirst($url[0]);
		$this->actions['action'] = $url[1];
		var_dump($url[2]);
		if (isset($url[2])) {
			$argument = $url[2] . $query_string;
		} else {
			$argument = $query_string;
		}
		var_dump($argument);
		parse_str($argument,$this->arguments);
	}

	public function parse_query_string() {
		$query_string = trim($_SERVER['QUERY_STRING'],"/");
		if (!$query_string) {
			return;
		}
		//判断是0模式还是2模式
		var_dump($query_string);
		if (strpos($query_string, "/") === false) {

			$url = explode("&",$query_string,3);
			$this->actions['controller'] = ucfirst(explode("=", $url[0],2)[1]);
			$this->actions['action'] = explode("=", $url[0],2)[1];

			if (isset($url[2])) {
				parse_str($url[2],$this->arguments);
			}

		} else {

			$url = explode("&",$query_string,2);
			$this->actions['controller'] = ucfirst(explode("/",explode("=",$url[0])[1])[0]);
			$this->actions['action'] = explode("/",explode("=",$url[0])[1])[1];

			if (isset($url[1])) {
				parse_str($url[1],$this->arguments);
			}
			
		}
	}

	public function generate_url() {
		$url = $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI'];
		$this->url = $url;
	}

}