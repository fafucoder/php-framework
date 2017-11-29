<?php
namespace core\system;

class Base {
	public static $classMap = array();
	public static function run()
	{
		p('ok');
		$route = new Route();

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
}