<?php
namespace core\system;
use core\system\Config;


class Log{
	static public $class;
	static public function init(){
		$driver = Config::get("type","log");
		$class = '\core\driver\log\\'.$driver;
		self::$class = new $class();
	}

	static public function log($name){
		self::$class->log($name);
	}
}