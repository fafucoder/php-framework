<?php
namespace System;

class Controller {

	/**
	 * 视图类
	 * @var [type]
	 */
	protected $view;

	/**
	 * 请求类
	 * @var [type]
	 */
	protected $request;

	/**
	 * 前置操作
	 * @var array
	 */
	protected $beforeAction = array();

	/**
	 * 后置操作
	 * @var array
	 */
	protected $afterAction = array();

	public function __construct(Request $request = null) {
		$this->view = View::$instance();
		$this->request = is_null($request) ? Request::instance() : $request;
		$this->_init();

		if ($this->beforeAction) {
			foreach ($this->beforeAction as $action => $options) {
				$this->beforeAction($action, $options);
			}
		}
	}

	public function _init(){
	}

	/**
	 * 前置操作
	 * @param  string $action
	 * @param  array $options
	 * @return mixed
	 */
	public function beforeAction($action, $options) {
		if (isset($optons['only'])) {
			if (is_string($options['only'])) {
				$options['only'] = explode(",", $options['only']);
			}
			if (!in_array($this->request->action(), $options['only'])) {
				return ;
			}
		} elseif (isset($options['except'])) {
			if (is_string($options['except'])) {
				$options['except'] = explode(",", $options['except']);
			}
			if (in_array($this->request->action(), $options['except'])) {
				return;
			}
		}

		if (isset($options['params'])) {
			if (is_string($options['params'])) {
				$params = explode(",", $options['params']);
			}
		}
		call_user_func_array(array($this, $method), isset($params) ? $params : []);
	}

	/**
	 * 后置操作
	 * @param  string $action
	 * @param  arrray $options
	 * @return mixed
	 */
	public function afterAction($action, $options) {
		if (isset($optons['only'])) {
			if (is_string($options['only'])) {
				$options['only'] = explode(",", $options['only']);
			}
			if (!in_array($this->request->action(), $options['only'])) {
				return ;
			}
		} elseif (isset($options['except'])) {
			if (is_string($options['except'])) {
				$options['except'] = explode(",", $options['except']);
			}
			if (in_array($this->request->action(), $options['except'])) {
				return;
			}
		}

		if (isset($options['params'])) {
			if (is_string($options['params'])) {
				$params = explode(",", $options['params']);
			}
		}
		call_user_func_array(array($this, $method), isset($params) ? $params : []);
	}


	public function display() {

	}

	public function show() {

	}

	public function assign() {

	}

	public function __set($name, $value) {
		$this->assign($name, $value);
	}

	public function __get($name) {

	}

	public function __isset($name) {

	}

	public function __call($method, $params) {

	}

	public function __callStatic($method, $params) {

	}

	public function jump() {

	}

	public function redirect() {

	}

	public function success() {

	}

	public function error() {

	}


}
