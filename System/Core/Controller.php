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
		$this->view = View::instance();
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
		call_user_func_array(array($this, $action), isset($params) ? $params : []);
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

	/**
	 * 渲染输出
	 * @param  string  $templateFile 模板文件
	 * @param  array   $data         数据
	 * @param  boolean $layout       是否模板文件
	 * @return string
	 */
	public function display($templateFile = '', $data = array(), $layout = true) {
		//判断是否有模板文件入股没有模板文件则获取当前类和方法
		if (!$templateFile) {
			$request = Request::instance();
			$templateFile = implode("/", $request->dispatch);
			$templateFile .= "." . Config::get('template.view_suffix');
		}
		$content = $this->view->display($templateFile, $data, $layout);
		$this->render($content);
	}

	public function show($templateFile = '', $data = array()) {
		//判断是否有模板文件入股没有模板文件则获取当前类和方法
		if (!$templateFile) {
			$request = Request::instance();
			$templateFile = implode("/", $request->dispatch);
			$templateFile .= "." . Config::get('template.view_suffix');
		}
		$content = $this->view->show($templateFile, $data);
		$this->render($content);
	}

	/**
	 * 输出内容
	 * @param  string $content 内容
	 * @return string
	 */
	private function render($content) {
		$content = preg_replace_callback(
			"/{__(\w+)__}/", 
			function($match) {
				if (defined($match[1])) {
					return constant($match[1]);
				}
				return $m;
			},
		$content);
		echo $content;
	}
	/**
	 * 变量赋值
	 * @param  string $name  
	 * @param  string $value 
	 * @return void
	 */
	public function assign($name, $value = '') {
		$this->view->assign($name, $value);
		return $this;
	}

	public function __set($name, $value) {
		$this->assign($name, $value);
	}

	public function get($name) {
		return $this->view->get($name);
	}

	public function __get($name) {
		return $this->get($name);
	}

	public function __isset($name) {
		return $this->get($name);
	}

   /**
     * 操作成功跳转的快捷方法
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    public function success($msg = '', $url = null, $data = '', $wait = 3, array $header = []){
        if (is_null($url) && !is_null(Request::instance()->server('HTTP_REFERER'))) {
            $url = Request::instance()->server('HTTP_REFERER');
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = Url::build($url);
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if ('html' == strtolower($type)) {
            $template = Config::get('template');
            $view = Config::get('view_replace_str');

            $result = ViewTemplate::instance($template, $view)
                ->fetch(Config::get('dispatch_success_tmpl'), $result);
        }

        $response = Response::create($result, $type)->header($header);

        return $response;
    }

    /**
     * 操作错误跳转的快捷方法
     * @param mixed  $msg    提示信息
     * @param string $url    跳转的 URL 地址
     * @param mixed  $data   返回的数据
     * @param int    $wait   跳转等待时间
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    public function error($msg = '', $url = null, $data = '', $wait = 3, array $header = []){
        if (is_null($url)) {
            $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = Url::build($url);
        }

        $type = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if ('html' == strtolower($type)) {
            $template = Config::get('template');
            $view = Config::get('view_replace_str');

            $result = ViewTemplate::instance($template, $view)
                ->fetch(Config::get('dispatch_error_tmpl'), $result);
        }

        $response = Response::create($result, $type)->header($header);
    }

    /**
     * URL 重定向
     * @param string    $url    跳转的 URL 表达式
     * @param array|int $params 其它 URL 参数
     * @param int       $code   http code
     * @param array     $with   隐式传参
     * @return void
     * @throws HttpResponseException
     */
    public function redirect($url, $params = [], $code = 302, $with = []){
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
    }

	public function ajax($data, $type = '', $json_option = 0) {
		if (empty($type)) {
			$type = Config::get('default_ajax_return');
		}
		switch (strtolower($type)) {
			case 'json':
				// 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                echo json_encode($data, $json_option);
                break;
			case 'xml':
				break;
			case 'jsonp':
				header('Content-Type:application/json; charset=utf-8');
				$handler = isset($_GET['jsonpcallback']) ? $_GET['jsonpcallback'] : '';
				echo $handler . "(" . json_encode($data, $json_option) . ")";
				break;
			default:
				header('Content-Type:application/json; charset=utf-8');
                echo json_encode($data, $json_option);
				break;
		}
	}

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(){
        return Request::instance()->isAjax() ? Config::get('default_ajax_return') : Config::get('default_return_type');
    }
}
