<?php 
namespace System;

class Hook {

	/**
	 * 钩子数组
	 * @var array
	 */
	public static $hooks = array();

	/**
	 * 添加钩子
	 * @param string  $hook   [description]
	 * @param mixed  $action [description]
	 * @param boolean $top    [description]
	 */
	public static function addHook($hook,$action,$top = false) {
		if (!in_array($hook, self::$hooks[$hook])) {
			self::$hooks[$hook] = array();
		}
		if (is_array($action)) {
			self::$hooks[$hook] = array_merge(self::$hooks[$hook], $action);
		} elseif ($top) {
			array_unshift(self::$hooks[$hook], $action);
		} else {
			self::$hooks[$hook][] = $action;
		}
	}

	/**
	 * 批量添加钩子
	 * @param array  $hooks     [description]
	 * @param boolean $recursive [description]
	 */
	public static function addHooks($hooks, $recursive = true) {
		if ($recursive) {
			foreach ($hooks as $hook => $behavior) {
				self::addHook($hook, $behavior);
			} 
		} else {
			self::$hooks = $hooks;
		}
	}

	/**
	 * 获取钩子
	 * @param  [type] $hook [description]
	 * @return [type]       [description]
	 */	
	public static function get($hook) {
		return array_key_exists($hook, self::$hooks) ? self::$hooks[$hook] : [];
	}
	/**
	 * 监听钩子
	 * @param  string $hook    钩子
	 * @param  mixed &$params 
	 * @param  mixed $extra   
	 * @return [type]          
	 */
	public static function listen($hook, &$params = null, $extra = null) {
		$result = array();
		foreach (self::get($hook) as $key => $value) {
			$result[$key] = self::exec($value, $hook, $params, $extra);
			if (false === $result[$key]) {
				break;
			}
		}
		return $results;
	}

	/**
	 * 执行钩子
	 * @param  [type] $class   [description]
	 * @param  string $hook    [description]
	 * @param  [type] &$params [description]
	 * @param  [type] $extra   [description]
	 * @return [type]          [description]
	 */
	public static function exec($class, $hook = '', &$params = null, $extra = null) {
		
	}

}