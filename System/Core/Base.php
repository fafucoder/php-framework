<?php
namespace core\system;
class Base {
	public static $classMap = array();
	public $assign;
	public static function run()
	{
		Log::init();
		$route = new \core\system\Route();
		$controller = ucfirst($route->controller);
		$action = ucfirst($route->action);
		$filename = APP.'/controllers/'.$controller.'Controller.php';
		if (is_file($filename)) {
			$class= "\application\controllers\\".$controller."Controller";
			$ctrl = new $class();
			$ctrl->$action();

			// 写入日志
			Log::log("controller:{$controller}  action:{$action}");
		} else {
			throw new Exception("找不到控制器", 1);
			
		}
	}

	public static function autoload($class)
	{
		$class = str_replace("\\",'/',$class);
		if (isset($classMap[$class])) {
			return $class;
		}
		$filename = MINI.'/'.$class.'.php';
		if (!is_file($filename)) 
		{
			return false;
		} 
		include $filename;
		self::$classMap[$class] = $class;

	}

	public function assign($name,$value) 
	{
		$this->assign[$name] = $value;
	}

	public function display($file)
	{
		$file = APP.'/views/'.$file;
		if (is_file($file)) {
			// extract()将数组变成独立的变量
			extract($this->assign);
			include $file;
		}
	}
}