<?php
namespace System;

class App {

    public static $dispatch;

    public static $init = false;

    public static $file = [];

	// public static function run() {
	// 	self::init();
	// }
	
	// public static function init() {
	// 	//创建应用目录
	// 	self::mkdir();
	// 	self::initCommon();
	// 	self::define_const();
	// 	self::loadConfig();
	// 	self::debug();
	// }
	
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
			exit;
			$request->dispatch($dispatch);
			//执行应用
			$data = self::exec($dispatch, $config);

		} catch (Exception $e) {
			return $e->getMessage();
		}
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
		$home = base_url();
		define("__HOME__",$home);
		define("__ROOT__",dirname(__HOME__) . SP );
		define("__PUBLIC__",PUBLIC_PATH);
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
        var_dump($result);
        exit;
	}

}
