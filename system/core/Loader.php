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
	protected $namespace = array();

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
	protected $includePath = array();

	public function registerNamespaces(array $namespacees, $merge = true) {

	}

	public function getNamespaces() {

	}

	public function registerPrefixes() {

	}

	public function getPrefixes() {

	}

	public function register() {

	}

	public function unregister() {
		
	}

	public static function import() {

	}



}