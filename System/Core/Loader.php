<?php
namespace System;

class Loader
{
	/**
	 * @var array 实例数组
	 */
	protected $instance = array();

	/**
	 * @var  namespace
	 */
	protected $namespaces = array();

	/**
	 * 类或者命名空间前缀
	 * @var array
	 */
	protected $prefixes = array();

	/**
	 * 类名映射
	 * @var array
	 */
	protected $map = array();

	/**
	 * [$classes description]
	 * @var array
	 */
	protected $classes = array();

	/**
	 * 保存include路径
	 * @var array
	 */
	protected $includePath = '';

	public function setIncludePath($path) {
		$this->includePath = $path;
	}

	public function getIncludePath() {
		return $this->includePath;
	}

	/**
	 * 注册命名空间
	 */
	public function registerNamespaces(array $namespaces, $merge = true) {
		if ($merge) {
			$this->namespaces = array_merge($this->namespaces,$namespaces);
		} else {
			$this->namespaces = $namespaces;
		}
		krsort($this->namespaces);
	}

	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * 注册类前缀
	 */
	public function registerPrefixes(array $prefixes = array(), $merge = true) {
		if ($merge) {
			$this->prefixes	= array_merge($this->prefixes, $prefixes);
		} else {
			$this->prefixes = $prefixes;
		}
		krsort($this->prefixes);
	}

	public function getPrefixes() {
		return $this->prefixes;
	}

	/**
	 * 注册类
	 */
	public function registerClasses(array $classes = array(), $merge = true) {
		if ($merge) {
			$this->classes = array_merge($this->classes, $classes);
		} else {
			$this->classes = $classes;
		}
	}

	public function getClasses() {
		return $this->classes;
	}

	public function register() {
		//设置根路径
		$this->setIncludePath(ROOT_PATH);

		//设置命名空间
		$this->registerNamespaces([
			'System' => 'System/Core'
		]);

		spl_autoload_register(array($this, 'autoload'),true,true);
	}

	public function unregister() {
		spl_autoload_unregister(array($this, 'autoload'));
	}

	public static function import($class, $baseUrl = '', $ext = EXT) {

	}

	public function autoload($class) {
		if ($file = $this->getFilePath($class)) {
			$this->requireFile($file);
			$this->loadedClasses[$class] = $file;
		}
	}

	public function getFilePath($class) {
		if (array_key_exists($class,$this->classes)) {
			if ($this->fileExists($this->classes[$class])) {
				return $this->classes[$class];
			}
		}
		
		if (false !== strrpos($class, "\\")) {
			$file = str_replace("\\",DIRECTORY_SEPARATOR,$class).'.php';
			if ($this->fileExists($file)) {
				return $file;
			}
		}

		if(false !== strrpos($class, "\\")) {
			foreach ($this->namespaces as $namespace => $dir) {
				if (strpos($class, $namespace . '\\') === 0) {
					$file = $dir . str_replace("\\",DIRECTORY_SEPARATOR,substr($class,strlen($namespace))) . '.php';
				}
				if ($this->fileExists($file)) {
					return $file;
				}
			}
		}

		foreach ($this->prefixes as $prefix => $dir) {
            if (substr($prefix, -1, 1) === '_') {
                $prefix = substr($prefix, 0, strlen($prefix) - 1);
            }
            if (strpos($class, $prefix . '_') === 0) {
                $file = $dir . str_replace('_', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';

                if ($this->fileExists($file)) {
                    return $file;
                }

                $file = $dir . str_replace('_', '', substr($class, strlen($prefix) + 1)) . '.php';

                if ($this->fileExists($file)) {
                    return $file;
                }
            }
        }
	}

	public function fileExists($file) {
		return file_exists($this->includePath . $file);
	}

	protected function requireFile($file) {
		$file = $this->includePath . $file;
		if (file_exists($file)) {
			require_once $file;
			// return true;
		}
		// return false;
	}

}
