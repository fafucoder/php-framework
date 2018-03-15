<?php
namespace core\driver\log;
use core\system\Config;
class File {

	public $path;
	public function __construct(){
		$this->path = Config::get("OPTION",'log')["PATH"].date("Ymd");
	}
	public function log($message,$file="log")
	{
		/**
		 * 确定文件存储位置是否存在
		 * 新建目录
		 * 写入日志
		 */
		if (!is_dir($this->path)) {
			mkdir($this->path);
		}
		$message = date("Y-m-d H:i:s")."\n".json_encode($message);
		file_put_contents($this->path."/".$file.'.php',$message.PHP_EOL,FILE_APPEND);
	}
}