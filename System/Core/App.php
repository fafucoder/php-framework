<?php
namespace System;

class App {

    public static $dispatch;

    public static $init = false;

    public static $file = [];

    public static $namespace = "Application";

    /**
     * @var bool 应用类库后缀
     */
    public static $suffix = false;

	public static function run(Request $request = null) {
		$request = is_null($request) ? Request::instance() : $request;
		try {
			//创建应用目录
			Build::checkDir();
			//加载公共文件
			self::initCommon();
			//加载常量
			self::defineConst();
			//加载配置
			$config = self::loadConfig();
			//信息调度
			$dispatch = self::$dispatch;
			if (empty($dispatch)) {
				$dispatch = self::routeCheck($request, $config);
			}
			$request->dispatch($dispatch);
			//执行应用
			$data = self::exec($dispatch, $config);
		} catch (Exception $e) {
			return $e->getMessage();
		}

        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            $type = $request->isAjax() ?
            Config::get('default_ajax_return') :
            Config::get('default_return_type');
            $response = Response::create($data, $type);
        } else {
            $response = Response::create();
        }
        return $response;
	}

	//加载 common
	public static function initCommon() {
		$common_path = array(
			"APP_COMMON"	=>	APP_PATH . 'Common/',
			'SYS_COMMON'	=>	CORE_PATH . 'Common/'
		);
		foreach ($common_path as $path) {
			$file = $path . 'functions.php';
			if (file_exists($file)) {
				require_once($file);
			}
		}
	}

	/**
	 * 加载配置项
	 * @return null
	 */
	public static function loadConfig() {
		$sys_config = CORE_PATH . 'Conf/default.php';
		Config::load($sys_config);

		$app_config = ['config','database'=>'database'];
		foreach ($app_config as $name => $file) {
			if (is_numeric($name)) {
				Config::load($file);
			} else {
				Config::load($file, $name);
			}
		}
		//load extra config
		$extra_file = Config::get('app.extra_file_list');
		if (!empty($extra_file)) {
			foreach ($extra_file as $filename) {
				$file = strpos($file, ".") ? $filename : APP_PATH . $filename . EXT;
				if (is_file($file)) {
					Config::load($file,pathinfo($filename, PATHINFO_FILENAME));
				}
			}
		}

		//load environment file
		$environment_config = ENVIRONMENT_PATH . ENVIRONMENT . EXT;
		if (file_exists($environment_config)) {
			Config::load($environment_config);
		}

		return Config::get();
	}

	/**
	 * 定义常量
	 * @return void
	 */
	public static function defineConst() {
        if (!defined('_PHP_FILE_')) {
            define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
        }
        if(!defined('ROOT')) {
            $_root  =   rtrim(dirname(_PHP_FILE_),'/');
            define('ROOT',  (($_root=='/' || $_root=='\\')?'':$_root));
        }
        if (!defined('PUBLIC')) {
            $public = ROOT . '/Public';
            define("PUBLIC",$public);
        }	
	}


	/**
	 * 开启debug
	 * @return null
	 * @todo  debug false 还没有完成
	 */
	public static function debug() {
		$debug = is_null(Config::get('debug')) ? DEBUG : Config::get('debug') ;
		if ($debug) {
			ini_set('display_errors', "On");
			$whoops = new \Whoops\Run();
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			$whoops->register();
		} else {
			ini_set('display_errors','Off');
		}
	}

    /**
     * 执行应用
     * @param  array $dispatch 调度信息
     * @param  array $config   配置信息
     * @return  mixed
     */
    public static function exec($dispatch, $config) {
        switch ($dispatch['type']) {
            case 'redirect':
                $data = Response::create($dispatch['url'], 'redirect')->code($dispatch['status']);
                break;
            case 'application':
                $data = self::application($dispatch['application'], $config);
                break;
        }
        return $data;
    }

    /**
     * 执行应用
     * @param  array $application controller/action
     * @param  array  $config      config
     * @return mixed
     */
    public static function application($application, $config = array()) {
        if (is_string($application)) {
            $application = explode("/", $application);
        }
        $request = Request::instance();

        $request->filter($config['default_filter']);
        $controller = strip_tags($application[0]) ?: $config['default_controller'];
        $action = strip_tags($application[1]) ?: $config['default_action'];
        try {
        	$instance = self::Controller($controller, $config['controller_suffix'], $config['empty_controller']);
        } catch (Exception $e) {
        	throw new \Exception(sprintf("Controller not exists:%s", $e->getClass()),404);
        }

        $action = $action . $config['action_suffix'];
        if (is_callable([$instance, $action])) {
        	$call = new Executable([$instance, $action]);
        } elseif (is_callable([$instance, '_empty'])) {
        	$call = new Executable([$instance,'_empty']);
        } else {
        	throw new \Exception(sprintf("method not exists %s", get_class($instance) . '->' . $action), 404);
        }
        $request->dispatch($application);
        $flection = $call->getReflection();
        $params = self::bindParams($flection);
        Session::init();
        return $call->invokeArgs($params);
    }

    /**
     * 定义控制器
     * @param string $controller        空控制器名称
     * @param string $controller_suffix 控制器前缀
     * @param string $empty_controller  空控制器
     */
    public static function Controller($controller, $controller_suffix, $empty_controller = '') {
        $namespace = self::$namespace . "\\Controller\\";

        if (!empty($controller_suffix)) {
            $controller= $namespace . self::ucFirst($controller) . self::ucFirst($controller_suffix);
            $empty = $namespace . self::ucFirst($empty_controller) . self::ucFirst($controller_suffix);
        }
        // var_dump($controller);
        // $controller = 'Application\Controller\CartController';
        // var_dump(class_exists($controller));exit;
        if (class_exists($controller)) {
            return self::invokeClass($controller);
        } elseif (class_exists($empty)) {
            return self::invokeClass($empty);
        }
        throw new \Exception("class not exist");
    }

    /**
     * [invokeClass description]
     * @param  [type] $class [description]
     * @param  array  $vars   [description]
     * @return [type]        [description]
     */
    public static function invokeClass($class, $vars = []) {
        $reflect = new \ReflectionClass($class);
        $constructor = $reflect->getConstructor();
        $args = $constructor ? self::bindParams($constructor, $vars) : [];
        return $reflect->newInstanceArgs($args);
    }

    /**
     * 参数绑定
     * @param  [type] $reflect [description]
     * @param  array  $vars    [description]
     * @return [type]          [description]
     */
    public static function bindParams($reflect, $vars = []) {
        if (empty($vars)) {
            $vars = Request::instance()->route();
        }
        $args = [];
        if ($reflect->getNumberOfParameters() > 0) {
            reset($vars);
            $type = key($vars) === 0 ? 1 : 0;
            foreach ($reflect->getParameters() as $param) {
                $args[] = self::getParamValue($param,$vars, $type);
            }
        }
        return $args;
    }

    /**
     * 获取参数值
     * @param  [type] $param [description]
     * @param  [type] $vars  [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
    public static function getParamValue($param, &$vars, $type) {
        $name = $param->getName();
        $class = $param->getClass();
        if ($class) {
            $className = $class->getName();
	        if (method_exists($className, 'invoke')) {
	            $method = new \ReflectionMethod($className, 'invoke');
	                if ($method->isPublic() && $method->isStatic()) {
	                    return $className::invoke(Request::instance());
	                }
	        }
	       	$result = method_exists($className, 'instance') ? $className::instance() : new $className;
        } elseif (1 == $type && !empty($vars)) {
            $result = array_shift($vars);
        } elseif (0 == $type && isset($vars[$name])) {
            $result = $vars[$name];
        } elseif ($param->isDefaultValueAvailable()) {
            $result = $param->getDefaultValue();
        } else {
            throw new \InvalidArgumentException('method param miss:' . $name);
        }
        return $result;
    }

    /**
     * 首字母大写
     * @param  string $string
     * @return string
     */
    public static function ucFirst($string) {
        return ucfirst(strtolower($string));
    }
    /**
     * 调度信息
     * @param  array $dispatch 调度信息
     * @return void
     */
    public static function dispatch($dispatch) {
        self::$dispatch = ['application' => $dispatch];
    }

	public static function routeCheck($request, array $config) {
		$path = $request->path();
		$depr   = $config['pathinfo_depr'];
        $result = false;
        $route_file = CONF_PATH . 'route' . CONF_EXT;
        if (is_file($route_file)) {
        	$rules = include $route_file;
        	is_array($rules) && Route::import($rules);
        }
        $result = Route::check($request, $path, $depr);
        if (false === $result) {
        	$result = Route::parseUrl($path, $depr);
        }
        
        return $result;
	}

}
