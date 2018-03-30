<?php
namespace System;

class Routes {

	/**
	 * 控制器名
	 */
	public static $controller = "Index";

	/**
	 * 方法名
	 * @var
	 */
	public static $action = "index";

	/**
	 * 路由规则
	 */
	public static $rules = array();

	/**
	 * config配置
	 */
	public static $config = array();

	/**
	 * 空控制器
	 */
	public static $empty_controller = "Empty";

	/**
	 * 类后缀
	 * @var string
	 */
	public static $class_suffix = "";

	/**
	 * 控制器后缀
	 * @var string
	 */
	public static $controller_suffix = "";

	/**
	 * 方法前缀
	 * @var string
	 */
	public static $action_prefix = "";

	/**
	 * 方法后缀
	 * @var string
	 */
	public static $action_suffix = "";

	/**
	 * 返回类型
	 * @var string
	 */
	public static $return_type = "";

	/**
	 * 路由模式
	 * @var integer
	 */
	public static $url_mode = 1;

	/**
	 * 路由分组
	 * @var array
	 */
	public static $group = array();

	public static function init() {
		self::$config = Config::get('app');
	}

	/**
	 * 导入路由规则
	 * @param  array $rules 路由规则
	 * @param  string $type  类型
	 * @return void        
	 */
	public static function import(array $rules = array(), $type = "*") {

	}

	/**
	 * 注册路由
	 * @param  array  $rules 路由规则
	 * @param  string $type  类型
	 * @return void        
	 */
	public static function registerRules(array $rules = array(), $type = "*") {
		foreach ($rules as $key => $value) {
			if (is_numeric($key)) {
				$key = array_shift($value);
			}
			if (empty($value)) {
				continue;
			}
			if (is_string($key) && 0 === strpos($key, "[")) {
				$key = substr($key, 1, -1);
				self::group($key, $value);
			} elseif (is_array($value)) {
				self::setRule($key, $value[0], $type, isset($value[1]) ?: [], isset($value[2]) ?: []);
			} else {
				self::setRule($key, $value, $type);
			}
		}
	}

	public static function setRules() {

	}

	/**
	 * 设置路由规则
	 * @param string|array $rule   路由名
	 * @param string $route  路由地址
	 * @param string $type   类型
	 * @param array  $option 参数
	 * @param array  $patten 规则
	 * @param string $group  分组
	 */
	public static function setRule($rule, $route, $type = "*", $option = [], $patten = [], $group = '') {
		if (is_array($rule)) {
			$name = $rule[0];
			$rule = $rule[1];
		} elseif (is_string($route)) {
			$name = $route;
		}
		//是否完全匹配
		if (!isset($option['complete_match'])) {
			if ("$" == substr($rule, -1,1)) {
				$option['complete_match'] = true;
			}
		} elseif (empty($option['complete_match']) && '$' == substr($rule, -1, 1)) {
			$option['complete_match'] = true;
		}	
		
		if ('$' == substr($rule, -1,1)) {
			$rule = substr($rule, 0,-1);
		}
		if ("/" != $rule || $group) {
			$rule = trim($rule, '/');
		}
		$vars = self::parseVar($rule);
	}

	/**
	 * 路由注册
	 * @param  string|array $rule   
	 * @param  string $route  
	 * @param  string $type   
	 * @param  array  $option 
	 * @param  array  $patten [description]
	 * @return void         
	 */
 	public static function rule($rule, $route = '', $type = '*', $option = [], $patten = []) {

	}	

	/**
	 * 路由分组
	 * @param  string|name $name   
	 * @param  array|Closure $routes 
	 * @param  array  $option 
	 * @param  array  $patten [description]
	 * @return void         
	 */
	public static function group($name,array $routes, $option = [], $patten = []) {
		if (is_array($name)) {
			$option = $name;
			$name = isset($option['name']) ?: '';
		}
		$currentGroup = self::getGroup('name');
		if ($currentGroup) {
			$name = $currentGroup . ($name ? '/'.ltrim($name,'/') : '');
		}
		if (!empty($name)) {
			if ($routes instanceof Closure) {
				$currentOption = self::getGroup('option');
				$currentPatten = self::getGroup('patten');
				self::setGroup($name, array_merge($currentOption, $option), array_merge($currentPatten, $patten));
				call_user_func_array($routes, []);
				self::setGroup($currentGroup, $currentOption, $currentPatten);

			}
		}
	}

	/**
	 * 设置路由分组
	 * @param string $name   
	 * @param array  $option [description]
	 * @param array  $patten [description]
	 */
	public static function setGroup($name, $option = [], $patten = []) {
		self::$group['name'] = $name;
		self::$group['option'] = $option;
		self::$group['patten'] = $patten;
	}

	/**
	 * 获取路由分组
	 * @param  string $type 
	 * @return  mixed
	 */
	public static function getGroup($type) {
		if (isset(self::$group[$type])) {
			return self:;$group[$type];
		} else {
			return 'name' == $type ? null : [];
		}
	}

	/**
	 * 变量提取
	 * @param  string|array $rule [description]
	 * @return array       
	 */
	private static function parseVar($rule) {
		$var = [];
		foreach (explode("/", $rule) as $value) {
			$optional = false;
			if (false !== strpos($var, "<") && preg_match_all('/<(\w+(\??))>/', $var, $matches)) {
				foreach ($matches[1] as $name) {
					if (strpos($name, "?")) {
						$name = substr($name, 0, -1);
						$optional = true;
					} else {
						$optional = true;
					}
					$var[$name] = $optional ? 2 : 1;
				}
			}
	        if (0 === strpos($val, '[:')) {
	            // 可选参数
	            $optional = true;
	            $val = substr($val, 1, -1);
	        }
	        if (0 === strpos($val, ':')) {
	            // URL变量
	            $name = substr($val, 1);
	            $var[$name] = $optional ? 2 : 1;
	        } 
	        return $var;
	    }
	}


}
