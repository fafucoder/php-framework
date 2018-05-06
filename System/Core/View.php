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
	public $twig;

	/**
	 * twig 缓存
	 */
	protected $twig_config = array(
		'auto_reload' => true,
	);

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
	private function __construct() {
		$this->config = Config::get('template');
		if (defined('VIEW_PATH')) {
			$this->view_path = VIEW_PATH;
		}
		$this->registerGlobal();

		$loader = new \Twig_Loader_Filesystem($this->view_path);
		if ($this->config['cache']) {
			$this->twig_config['cache'] = CACHE_PATH;
		}
		$this->twig_config['debug'] = $this->config['debug'];
		$this->twig = new \Twig_Environment($loader, $this->twig_config);
	}

	/**
	 * 唯一实例化
	 * @return View
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * 变量赋值
	 * @param string|array $name
	 * @param string $value
	 * @return void;
	 */
	public function assign($name, $value = '') {
		if (is_array($name)) {
			$this->data = array_merge($this->data, $name);
		} else {
			$this->data[$name] = $value;
		}
	}

	/**
	 * 直接渲染输出
	 * @param  string $template 模板文件
	 * @param  string $data     数据
	 * @return content
	 */
	public function show($template='', $data = array()) {
		if ($data) {
			$this->data = array_merge($this->data, $data);
		}
		return $this->render($template, $this->data);
	}

	/**
	 * 渲染输出
	 * @param  string  $templateFile [description]
	 * @param  boolean $layout       [description]
	 * @return [type]                [description]
	 */
	public function display($templateFile = '', $data = array(), $layout = true) {
		if ($data) {
			$this->data = array_merge($this->data, $data);
		}
		if ($layout && $this->config['layout_on']) {
			return $this->parseLayout($templateFile);
		} else {
			return $this->parseTemplate($templateFile);
		}
	}

	/**
	 * 解析模板文件
	 * @param  string $templete 模板文件
	 * @return strting
	 */
	public function parseTemplate($templateFile = '') {
		$file = $this->view_path . "/" . strtolower($templateFile);
		if (!file_exists($file)) {
			return false;
		}
		$content = $this->render($templateFile, $this->data);
		return $content;
	}

	/**
	 * 解析布局
	 * @param  string $layoutFile [description]
	 * @return
	 */
	public function parseLayout($layoutFile = '') {
		if ($this->config['layout_on']) {
			$this->layout = $this->config['layout_path'];
			$layout_file = $this->view_path . 'layout/' . $this->layout . '.' . $this->config['view_suffix'];
			if (!is_file($layout_file)) {
				return false;
			}
			$layout_file = 'layout/' . $this->layout . '.' . $this->config['view_suffix'];

			$content = $this->parseTemplate($layoutFile);

			$layout_file = $this->render($layout_file, $this->data);

			$content = str_replace($this->config['layout_content_replace'], $content, $layout_file);

			return $content;

		} else {

			return $this->parseTemplate($layoutFile);
		}


	}

	public function render($template, $data) {
		return $this->twig->render($template, $data);
	}

	/**
	 * 注册全局变量
	 */
	public function registerGlobal() {
		$setting_file = CONF_PATH . 'setting.php';
		if (file_exists($setting_file)) {
			$settings = include($setting_file);
			if (is_array($settings)) {
				foreach ($settings as $name => $value) {
					$this->data[$name] = $value;
				}
			}
		}
	}

	/**
	 * 获取数据
	 * @param  string $name 名称
	 * @return mixed       
	 */
	public function get($name = '') {
		if ('' === $name) {
			return $this->data;
		}
		return isset($this->data[$name]) ? $this->data[$name] : false;
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
