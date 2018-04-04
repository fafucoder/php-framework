<?php 
namespace System;

class View {

	/**
	 * 实例化
	 * @var [type]
	 */
	public static $instance;

	/**
	 * 布局文件
	 * @var [type]
	 */
	protected $layout;

	/**
	 * view路径
	 * @var [type]
	 */
	protected $view_path;

	/**
	 * twig模板引擎
	 * @var [type]
	 */
	protected $twig;

	/**
	 * 配置文件
	 * @var array
	 */
	protected $config = array();

	/**
	 * 变量数据
	 * @var array
	 */
	protected $data = array();

	/**
	 * 构造器
	 */
	private static function __construct() {
		$this->config = Config::get('templete');
		if ($this->config['layout_on']) {
			$this->layout = $this->config['layout_path'];
		} 
		$this->twig = new \Twig_Loader_Filesystem ($this->layout); 
	}

	/**
	 * 唯一实例化
	 * @return View 
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}



	public function assign($name, $value = '') {
		if (is_array($name)) {
			$this->data = array_merge($this->data, $name);
		} else {
			$this->data[$name] = $value;
		}
		return $this;
	}

	public function render() {

	}

	public function __set($name, $value) {
		$this->data[$name] = $value;	
	}

	public function __get($name) {
		return $this->data[$name];
	}

	public function __isset($name) {
		return isset($this->data[$name]);
	}
}