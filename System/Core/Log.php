<?php 
namespace System;

class Log {
	/**
	 * 错误级别类型
	 * @var array
	 */
	protected static $log_ext = "";

	/**
	 * 保存日志
	 * @var array
	 */	
	protected static $log  = "";

	/**
	 * 保存路径
	 * @var [type]
	 */
    protected static $save_path;

	/**
	 * 存储类型
	 * @var [type]
	 */
	protected static $save_type ;


	public static function init() {
		$log = Config::get('log');
		self::$save_type = isset($log['type']) ? strtolower($log['type']) : "file";
		self::$save_path = isset($log['path']) ? $log['path'] : null ;
		self::$log_ext = isset($log['ext']) ? $log['ext'] : EXT ;
	}

	public static function save($message) {
		if (empty($message)) {
			return;
		}
		self::$log  = "$message ";
		ob_clean();
	}

	/**
	 * 日志写入
	 * @param  [type] $message [description]
	 * @param  [type] $level   [description]
	 * @param  [type] $type    [description]
	 * @param  [type] $dest    [description]
	 * @return [type]          [description]
	 */
	public static function write($message, $dest= "" ) {
		self::init();
		$dest = empty($dest) ? self::$save_path . date('Ymd') . self::$log_ext : $dest;
		$class = '\\System\Drivers\\Log\\'. ucwords(self::$save_type);
		(new $class)->write($message, $dest);
	}

	public static function show() {
		return self::$log;
	}

}