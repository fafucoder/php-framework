<?php
namespace System;

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

class Log
{
    const LOG    = 'log';
    const ERROR  = 'error';
    const INFO   = 'info';
    const SQL    = 'sql';
    const NOTICE = 'notice';
    const ALERT  = 'alert';
    const DEBUG  = 'debug';

    /**
     * @var array 日志信息
     */
    protected static $log = [];


    protected static $path ;

    protected static $level = array();

    protected static $writable;

    /**
     * @var array 配置参数
     */
    protected static $config = [];

    /**
     * @var array 日志类型
     */
    protected static $type = ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'];

    public static function __construct($config = []) {
    	self:$config = empty($config) ?: Config::get('log');

    	self::$path = isset(self::$config['path']) ?: Config::get('log.path');
    	file_exists(self::$path) OR mkdir(self::$path,0777);
    	if ( ! is_dir(self::$_log_path) OR ! is_really_writable(self::$_log_path))
		{
			self::$writable = FALSE;
		}
		self::$level = isset($config['level']) ?: Config::get('log.level');
    }
    /**
     * 获取日志信息
     * @access public
     * @param  string $type 信息类型
     * @return array|string
     */
    public static function getLog($type = '')
    {
        return $type ? self::$log[$type] : self::$log;
    }

    /**
     * 清空日志信息
     * @access public
     * @return void
     */
    public static function clear()
    {
        self::$log = [];
    }

    /**
     * 保存调试信息
     * @access public
     * @return bool
     */
    public static function save()
    {
        // 没有需要保存的记录则直接返回
        if (empty(self::$log)) {
            return true;
        }

        is_null(self::$driver) && self::init(Config::get('log'));

        // 检测日志写入权限
        if (!self::check(self::$config)) {
            return false;
        }

        if (empty(self::$config['level'])) {
            // 获取全部日志
            $log = self::$log;
            if (!App::$debug && isset($log['debug'])) {
                unset($log['debug']);
            }
        } else {
            // 记录允许级别
            $log = [];
            foreach (self::$config['level'] as $level) {
                if (isset(self::$log[$level])) {
                    $log[$level] = self::$log[$level];
                }
            }
        }

        if ($result = self::$driver->save($log)) {
            self::$log = [];
        }

        Hook::listen('log_write_done', $log);

        return $result;
    }

    /**
     * 实时写入日志信息 并支持行为
     * @access public
     * @param  mixed  $msg   调试信息
     * @param  string $type  信息类型
     * @param  bool   $force 是否强制写入
     * @return bool
     */
    public static function write($msg, $type = 'log', $force = false)
    {
        $log = self::$log;

        // 如果不是强制写入，而且信息类型不在可记录的类别中则直接返回 false 不做记录
        if (true !== $force && !empty(self::$config['level']) && !in_array($type, self::$config['level'])) {
            return false;
        }

        // 封装日志信息
        $log[$type][] = $msg;

        // 监听 log_write
        Hook::listen('log_write', $log);

        is_null(self::$driver) && self::init(Config::get('log'));

        // 写入日志
        if ($result = self::$driver->save($log)) {
            self::$log = [];
        }

        return $result;
    }

    /**
     * 静态方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$type)) {
            array_push($args, $method);

            call_user_func_array('\\think\\Log::record', $args);
        }
    }

}

class CI_Log {

	/**
	 * Path to save log files
	 *
	 * @var string
	 */
	protected $_log_path;

	protected $levels;

	/**
	 * Array of threshold levels to log
	 *
	 * @var array
	 */
	protected $_threshold_array = array();

	/**
	 * Format of timestamp for log files
	 *
	 * @var string
	 */
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * Filename extension
	 *
	 * @var	string
	 */
	protected $_file_ext;

	/**
	 * Whether or not the logger can write to the log files
	 *
	 * @var bool
	 */
	protected $_enabled = TRUE;

	/**
	 * Predefined logging levels
	 *
	 * @var array
	 */
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);


	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$config =& get_config();

		self::$_log_path = ($config['log_path'] !== '') ? $config['log_path'] : APPPATH.'logs/';
		self::$_file_ext = (isset($config['log_file_extension']) && $config['log_file_extension'] !== '')
			? ltrim($config['log_file_extension'], '.') : 'php';

		file_exists(self::$_log_path) OR mkdir(self::$_log_path, 0755, TRUE);

		if ( ! is_dir(self::$_log_path) OR ! is_really_writable(self::$_log_path))
		{
			self::$_enabled = FALSE;
		}

		if (is_numeric($config['log_threshold']))
		{
			self::$_threshold = (int) $config['log_threshold'];
		}
		elseif (is_array($config['log_threshold']))
		{
			self::$_threshold = 0;
			self::$_threshold_array = array_flip($config['log_threshold']);
		}

		if ( ! empty($config['log_date_format']))
		{
			self::$_date_fmt = $config['log_date_format'];
		}

		if ( ! empty($config['log_file_permissions']) && is_int($config['log_file_permissions']))
		{
			self::$_file_permissions = $config['log_file_permissions'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{
		if (self::$_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if (( ! isset(self::$_levels[$level]) OR (self::$_levels[$level] > self::$_threshold))
			&& ! isset(self::$_threshold_array[self::$_levels[$level]]))
		{
			return FALSE;
		}

		$filepath = self::$_log_path.'log-'.date('Y-m-d').'.'.self::$_file_ext;
		$message = '';

		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;
			// Only add protection to php files
			if (self::$_file_ext === 'php')
			{
				$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos(self::$_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE);
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format(self::$_date_fmt);
		}
		else
		{
			$date = date(self::$_date_fmt);
		}

		$message .= self::$_format_line($level, $date, $msg);

		for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, self::substr($message, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, self::$_file_permissions);
		}

		return is_int($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Format the log line.
	 *
	 * This is for extensibility of log formatting
	 * If you want to change the log format, extend the CI_Log class and override this method
	 *
	 * @param	string	$level 	The error level
	 * @param	string	$date 	Formatted date string
	 * @param	string	$message 	The log message
	 * @return	string	Formatted log line with a new line character '\n' at the end
	 */
	protected function _format_line($level, $date, $message)
	{
		return $level.' - '.$date.' --> '.$message."\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe strlen()
	 *
	 * @param	string	$str
	 * @return	int
	 */
	protected static function strlen($str)
	{
		return (self::$func_overload)
			? mb_strlen($str, '8bit')
			: strlen($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Byte-safe substr()
	 *
	 * @param	string	$str
	 * @param	int	$start
	 * @param	int	$length
	 * @return	string
	 */
	protected static function substr($str, $start, $length = NULL)
	{
		if (self::$func_overload)
		{
			// mb_substr($str, $start, null, '8bit') returns an empty
			// string on PHP 5.3
			isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
			return mb_substr($str, $start, $length, '8bit');
		}

		return isset($length)
			? substr($str, $start, $length)
			: substr($str, $start);
	}
}

