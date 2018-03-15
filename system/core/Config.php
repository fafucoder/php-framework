<?php 
namespace core\system;

class Config {
	static public $conf =array();
	static function get($name,$file) 
	{
		$name =strtoupper($name);
		$file = ucfirst($file);
		if (isset(self::$conf[$name])) {
			return self::$conf[$file][$name];
		}
		$file = MINI."/core/config/".$file.".php";
		if (is_file($file)) {
			$conf = include $file;
			if (isset($conf[$name])) {
				self::$conf[$file] = $conf;
				return $conf[$name];
			} else {
				throw new \Exception("没有该配置项",$name);
			}
		} else {
			throw new \Exception("找不到对应的配置文件", $name);
		}
	}

// 返回文件的所有配置
	static public function all($file)
	{
		$file = MINI.'/core/config/'.ucfirst($file).'.php';
		if (is_file($file)) {
			$conf = include $file;
			return $conf;
		}
	}
}