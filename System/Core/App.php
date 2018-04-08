<?php
namespace System;

class App {
	const confstr = <<<str
	<?php
	return [
		//返回数组格式 key => value
	];
str;
	
    public static $config = array();

    public static $dispatch;

	public static function run() {
		self::init();
		
	}

	public static function init() {
		//创建应用目录
		self::mkdir();
		self::initCommon();
		self::define_const();
		self::loadConfig();
		self::debug();
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
		if (is_null(Config::findFile('config.php'))) {
			file_put_contents(CONF_PATH . 'config.php',confstr);
		}
		Config::load('config');
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

		//load setting file
		$setting_file = CONF_PATH . 'setting.php';
		if (file_exists($setting_file)) {
			$settings = include_once($setting_file);
			foreach ($settings as $name => $value) {
				$GLOBALS[$name] = $value;
			}
		} else {
			file_put_contents($setting_file,confstr);
		}
	}

	/**
	 * 创建目录
	 * @return null
	 */
	public static function mkdir() {
		$dir_path = array(APP_PATH,COMMON_PATH,CONF_PATH,ENVIRONMENT_PATH);
		foreach ($dir_path as $dir) {
			is_dir($dir) || mkdir($dir,0777,true);
		}
	}

	/**
	 * 定义常量			
	 * @return void
	 */
	public static function define_const() {
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

	public static function routeCheck($request) {

	}
}