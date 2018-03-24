<?php
namespace System;

class Loader
{
	/**
	 * @var array 实例数组
	 */
	protected static $instance = array();

	/**
	 * @var  namespace
	 */
	protected static $namespaces = array();

	/**
	 * 类或者命名空间前缀
	 * @var array
	 */
	protected static $prefixes = array();

	/**
	 * 类名映射
	 * @var array
	 */
	protected static $classes = array();

	/**
	 *已加载的类
	 */
	protected static $loaded_class = array();

	/**
	 * 保存include路径
	 * @var array
	 */
	protected static $includePath = '';

	/**
	 * 设置include的路径
	 * @param string $path 路径
	 */
	public static function setIncludePath($path) {
		self::$includePath = $path;
	}

	/**
	 * 获取include路径
	 * @return string 路径
	 */
	public static function getIncludePath() {
		return self::$includePath;
	}

	/**
	 * 注册命名空间
	 */
	public static function registerNamespaces(array $namespaces, $merge = true) {
		if ($merge) {
			self::$namespaces = array_merge(self::$namespaces, $namespaces);
		} else {
			self::$namespaces = $namespaces;
		}
		krsort(self::$namespaces);
	}

	public static function getNamespaces() {
		return self::$namespaces;
	}

	/**
	 * 注册类前缀
	 */
	public static function registerPrefixes(array $prefixes = array(), $merge = true) {
		if ($merge) {
			self::$prefixes	= array_merge(self::$prefixes, $prefixes);
		} else {
			self::$prefixes = $prefixes;
		}
		krsort(self::$prefixes);
	}

	/**
	 * 获取类前缀
	 * @return [type] [description]
	 */
	public static function getPrefixes() {
		return self::$prefixes;
	}

	/**
	 * 注册类
	 */
	public static function registerClasses(array $classes = array(), $merge = true) {
		if ($merge) {
			self::$classes = array_merge(self::$classes, $classes);
		} else {
			self::$classes = $classes;
		}
	}

	public static function getClasses() {
		return self::$classes;
	}

	public static function register() {
		//设置根路径
		self::setIncludePath(ROOT_PATH);

		//设置命名空间
		self::registerNamespaces([
			'System' => 'System/Core'
		]);

		spl_autoload_register(array('System\\Loader','autoload'),true,true);
	}

	public static function unregister() {
		spl_autoload_unregister(array('System\\Loader', 'autoload'));
	}

	public static function import($class, $baseUrl = '', $ext = EXT) {

	}

	public static function autoload($class) {
		if ($file = self::getFilePath($class)) {
			self::requireFile($file);
			self::$loaded_class[$class] = $file;
		}
	}

	public static function getFilePath($class) {
		if (array_key_exists($class, self::$loaded_class)) {
			if (self::fileExists(self::$loaded_class[$class])) {
				return self::$loaded_class[$class];
			}
		}
		

		if(false !== strrpos($class, "\\")) {
			foreach (self::$namespaces as $namespace => $dir) {
				if (strpos($class, $namespace . '\\') === 0) {
					$file = $dir . str_replace("\\",DIRECTORY_SEPARATOR,substr($class,strlen($namespace))) . '.php';
				}
				if (self::fileExists($file)) {
					return $file;
				}
			}
		}

		if (false !== strrpos($class, "\\")) {
			$file = str_replace("\\",DIRECTORY_SEPARATOR,$class).'.php';
			if (self::fileExists($file)) {
				return $file;
			}
		}

		foreach (self::$prefixes as $prefix => $dir) {
            if (substr($prefix, -1, 1) === '_') {
                $prefix = substr($prefix, 0, strlen($prefix) - 1);
            }
            if (strpos($class, $prefix . '_') === 0) {
                $file = $dir . str_replace('_', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';

                if (self::fileExists($file)) {
                    return $file;
                }

                $file = $dir . str_replace('_', '', substr($class, strlen($prefix) + 1)) . '.php';

                if (self::fileExists($file)) {
                    return $file;
                }
            }
        }
	}

	public static function fileExists($file) {
		return file_exists(self::$includePath . $file);
	}

	protected static function requireFile($file) {
		$file = self::$includePath . $file;
		if (file_exists($file)) {
			require_once $file;
		}
	}

}
