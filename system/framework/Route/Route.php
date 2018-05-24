<?php
namespace System;

class Route {

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
	 * 路由分组
	 * @var array
	 */
	public static $group = array();

	/**
	 * 路由规则
	 * @var array
	 */
	public static $rules = array(
	    'get'     => [],
        'post'    => [],
        'put'     => [],
        'delete'  => [],
        'patch'   => [],
        'head'    => [],
        'options' => [],
        '*'       => [],
        'alias'   => [],
        'patten'  => [],
        'name'    => [],
    );

	/**
	 * 路由参数
	 * @var array
	 */
    public static $option = array();

	public static function init() {
		self::$config = Config::get('app');
	}

	public static function import(array $rule, $type = "*") {
		if (isset($rule['__patten__'])) {
			self::patten($rule['__patten__']);
			unset($rule['__patten__']);
		}
		if (isset($rule['__alias__'])) {
			self::alias($rule['__alias__']);
			unset($rule['__alias__']);
		}
		self::registerRules($rule, strtolower($type));
	}
	/**
	 * 变量规则
	 * @param  string|array $name
	 * @param  string $rule
	 * @return void
	 */
	public static function patten($name = null, $rule = '') {
		if (is_array($name)) {
			self::$rules['patten'] = array_merge(self::$rules['patten'], $name);
		} else {
			self::$rules['patten'][$name] = $rule;
		}
	}


	/**
	 * 设置或者获取路由标识
	 * @param  string $name  [description]
	 * @param  array $value
	 * @return array
	 */
	public static function name($name = '', $value = null) {
		if (is_array($name)) {
			return self::$rules['name'] = $name;
		} elseif ('' === $name) {
			return self::$rules['name'];
		} elseif (!is_null($value)) {
			self::$rules['name'][strtolower($name)] = $value;
		} else {
			$name = strtolower($name);
			return isset(self::$rules['name'][$name]) ? self::$rules['name'][$name] : null;
		}
	}

	/**
	 * 获取或者批量设置路由
	 * @param  mixed $rules 请求类型或者定义数组
	 * @return array
	 */
	public static function rules($rules = '') {
		if (is_array($rules)) {
			self::$rules = $rules;
		} elseif ($rules) {
			return true === $rules ? self::$rules : self::$rules[strtolower($rules)];
		} else {
			$rules = self::$rules;
			unset($rules['patten'], $rules['alias'], $rules['name']);
			return $rules;
		}
	}

	/**
	 * 注册路由规则
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  string $type   请求类型
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function rule($rule, $route = '', $type = "*", $option = [], $patten = []) {
		$group = self::getGroup('name');

		if (!is_null($group)) {
			$option = array_merge(self::getGroup('option'), $option);
			$patten = array_merge(self::getGroup('patten'), $patten);
		}
		$type = strtolower($type);

		if (strpos($type, "|")) {
			$option['method'] = $type;
			$type = "*";
		}
		if (is_array($rule) && empty($route)) {
			foreach ($rule as $key => $value) {
				if (is_numeric($key)) {
					$key = array_shift($value);
				}
				if (is_array($value)) {
					$route = $value[0];
					$option1 = array_merge($option, isset($value[1]) ? $value[1] : []);
					$patten1 = array_merge($patten, isset($value[2]) ? $value[2] : []);
				} else {
					$option1 = null;
					$patten1 = null;
					$route = $value;
				}
				self::setRoute($key, $route, $type, !is_null($option1) ? $option1 : $option, !is_null($patten1) ? $patten1 : $patten, $group);
			}
		} else {
			self::setRule($rule, $route, $type, $option, $patten, $group);
		}
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

		if (isset($name)) {
			$key = $group ? $group . ($rule ? "/" . $rule : '') : $rule;
			$suffix = isset($option['ext']) ? $option['ext'] : null;
			self::name($name, [$key, $vars, $suffix]);
		}

		if ($group) {
			if ('*' != $type) {
				$option['method'] = $type;
			}
			self::$rules['*'][$group]['rule'][] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'patten' => $patten];
		} else {
			if ('*' != $type && isset(self::$rules['*'][$rule])) {
				unset(self::$rules['*'][$rule]);
			} elseif ('*' == $type) {
				foreach (['get', 'post', 'put', 'delete', 'patch', 'head', 'options'] as $method) {
					if (!isset(self::$rules[$method][$rule])) {
						self::$rules[$method][$rule] = true;
					}
				}
			}
			self::$rules[$type][$rule] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'patten' => $patten];
		}
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
				self::setRule($key, $value[0], $type, isset($value[1]) ? $value[1] : [], isset($value[2]) ? $value[2] : []);
			} else {
				self::setRule($key, $value, $type);
			}
		}
	}

	/**
	 * 路由分组
	 * @param  string|name $name
	 * @param  array|Closure $routes
	 * @param  array  $option
	 * @param  array  $patten 变量规则
	 * @return void
	 * @todo [description]
	 */
	public static function group($name, $routes, $option = [], $patten = []) {
		if (is_array($name)) {
			$option = $name;
			$name = isset($option['name']) ? $option['name'] : '';
		}
		$currentGroup = self::getGroup('name');
		if ($currentGroup) {
			$name = $currentGroup . ($name ? '/'.ltrim($name,'/') : '');
		}
		if (!empty($name)) {
			if ($routes instanceof \Closure) {
				$currentOption = self::getGroup('option');
				$currentPatten = self::getGroup('patten');
				self::setGroup($name, array_merge($currentOption, $option), array_merge($currentPatten, $patten));
				call_user_func_array($routes, []);
				self::setGroup($currentGroup, $currentOption, $currentPatten);
				if ($currentGroup != $name) {
					self::$rules['*'][$name]['route'] = '';
					self::$rules['*'][$name]['var'] = self::parseVar($name);
					self::$rules['*'][$name]['option'] = $option;
					self::$rules['*'][$name]['patten'] = $patten;
				}
			} else {
				$item = [];
				foreach ($routes as $key => $value) {
					if (is_numeric($key)) {
						$key = array_shift($value);
					}
					if (is_array($value)) {
						$route = $value[0];
						$option1 = array_merge($option, isset($value[1]) ? $value[1] : []);
						$patten1 = array_merge($patten, isset($value[2]) ? $value[2] : []);
					} else {
						$route = $value;
					}
					$options = isset($option1) ? $option1 : $option;
					$pattens = isset($patten1) ? $patten1 : $patten;
					if ('$' == substr($key, -1, 1)) {
						$options['complete_match'] = true;
						$key = substr($Key, 0, -1);
					}
					$key = trim($key, '/');
					$vars = self::parseVar($key);
					$item[] = ['rule' => $key, 'route' => $route, 'var' => $vars, 'option' => $options, 'patten' => $pattens];

					$suffix = isset($options['ext']) ? $options['ext'] : null;

					self::name($route, [$name . ($key ? '/' .$key :""), $vars, $suffix]);
				}
				self::$rules['*'][$name] = ['rule' => $item, 'route' => '', 'var' => [], 'option' => $option , 'patten' => $patten];
			}
			foreach (['get', 'post', 'put', 'delete', 'patch', 'head', 'options'] as $method) {
				if (!isset(self::$rules[$method][$name])) {
					self::$rules[$method][$name] = true;
				} elseif (is_array(self::$rules[$method][$name])) {
					self::$rules[$method][$name] = array_merge(self::$rules['*'[$name]], self::$rules[$method][$name]);
				}
			}

		} elseif ($routes instanceof \Closure) {
			$currentOption = self::getGroup('option');
			$currentPatten = self::getGroup('patten');
			self::setGroup('', array_merge($currentOption, $option), array_merge($currentPatten, $patten));
			call_user_func_array($routes, []);
			self::setGroup($currentGroup, $currentOption, $currentPatten);
		} else {
			self::rule($routes, '','*', $option, $patten);
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
			return self::$group[$type];
		} else {
			return 'name' == $type ? null : [];
		}
	}

	/**
	 * 设置参数信息
	 * @param array $options
	 */
	public static function setOption($options = []) {
		self::$option = $options;
	}

	/**
	 * 获取选项
	 * @return [type] [description]
	 */
	public static function getOption() {
		return self::$option;
	}

	/**
	 * 注册任意路由
	 * @param  string|array $rule
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function any($rule, $route = '', $option = [], $patten = []) {
		self::$rule($rule, $route, '*', $option, $patten);
	}

	/**
	 * 注册get路由
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
 	public static function get($rule, $route = '', $option = [], $patten = []) {
 		self::rule($rule, $route, "GET", $option, $patten);
	}

	/**
	 * 注册post路由
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function post($rule, $route = '', $option = [], $patten = []) {
		self::rule($rule, $route, "POST", $option, $patten);
	}

	/**
	 * 注册put路由
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function put($rule, $route = '', $option = [], $patten = []) {
		self::rule($rule, $route, "PUT", $option, $patten);
	}

	/**
	 * 注册delete路由
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function delete($rule, $route = '', $option = [], $patten = []) {
		self::rule($rule, $route, "DELETE", $option, $patten);
	}

	/**
	 * 注册patch路由
	 * @param  string|array $rule   路由规则
	 * @param  string $route  路由地址
	 * @param  array  $option 路由参数
	 * @param  array  $patten 变量规则
	 * @return void
	 */
	public static function patch($rule, $route = '', $option = [], $patten = []) {
		self::rule($rule, $route, "PATCH", $option, $patten);
	}

	/**
	 * 注册路由别名
	 * @param  string|array $rule   路由别名
	 * @param  string $route  路由地址
	 * @param  array  $option 参数
	 * @return void
	 */
	public static function alias($rule = null, $route = '', $option = []) {
		if (is_array($rule)) {
			self::$rules['alias'] = array_merge(self::$rules['alias'], $rule);
		} else {
			self::$rules['alias'][$rule] = $option ? [$route, $option] : $route;
		}
	}

	/**
	 * URL路由检测
	 * @param  Request $request Request请求对象
	 * @param  string $url     url地址
	 * @param  string $depr    url分隔符
	 * @return false|array
	 */
	public static function check($request, $url, $depr = '/') {
		$url = str_replace($depr, "|", $url);
		if (isset(self::$rules['alias'][$url]) || isset(self::$rules['alias'][strstr($url, '|', true)])) {
			$result = self::checkRouteAlias($request, $url, $depr);
			if (false !== $result) {
				return $result;
			}
		}
		$method = strtolower($request->method());
		$rules = isset(self::$rules[$method]) ? self::$rules[$method] : [];
		$item = str_replace("|", "/", $url);
		if (isset($rules[$item])) {
			$rule = $rules[$item];
			if (true === $rule) {
				$rule = self::$rules['*'][$item];
			}
			if (!empty($rule['route']) && self::checkOption($rule['option'], $request)) {
				self::setOption($rule['option']);
				return self::parseRule($item, $rule['route'], $url, $rule['option']);
			}
		}

		if (!empty($rules)) {
			return self::checkRoute($request, $rules, $url, $depr);
		}
		return false;
	}

	/**
	 * 检测路由规则
	 * @param  Request $request request请求
	 * @param  array $rules   路由规则
	 * @param  string $url     url地址
	 * @param  string $depr    url分隔符
	 * @param  string $group   路由分组
	 * @param  array  $option  路由参数
	 * @return mixed
	 */
	public static function checkRoute($request, $rules, $url, $depr = '/', $group = '', $option = []) {
		foreach ($rules as $key => $item) {
			if (true === $item) {
				$item = self::$rules['*'][$key];
			}
			if (!isset($item['rule'])) {
				continue;
			}
			$rule = $item['rule'];
			$route = $item['route'];
			$vars = $item['var'];
			$option = $item['option'];
			$patten = $item['patten'];

			if (!self::checkOption($option, $request)) {
				continue;
			}
			if (isset($option['ext'])) {
				$url = preg_match("/\." . $request->ext() . "$/i", '', $url);
			}
			if (is_array($rule)) {
				$pos = strpos(str_replace("<", ":", $key), ":");
				if (false !== $pos) {
					$str = substr($key, 0, $pos);
				} else {
					$str = $key;
				}
				if (is_string($str) && $str && 0 != stripos(str_replace("|", "/", $url), $str)) {
					continue;
				}
				self::setOption($option);
				$result = self::checkRoute($request, $rule, $url, $depr, $key, $option);
				if (false !== $result) {
					return $result;
				}
			} elseif ($route) {
				if ($group) {
					$rule = $group . ($rule ? '/' . ltrim($rule, '/') : '');
				}
				self::setOption($option);
				$result = self::checkRule($rule, $route, $url, $patten, $option, $depr);
				if (false !== $result) {
					return $result;
				}
			}
		}
		return false;
	}
	/**
	 * 检测路由别名
	 * @param  Request $request
	 * @param  string $url     url地址
	 * @param  string $depr    url分隔符
	 * @return mixed
	 */
	public static function checkRouteAlias($request, $url, $depr) {
		$array = explode("|", $url);
		$alias = array_shift($array);
		$item = self::$rules['alias'][$alias];

		if (is_array($item)) {
			list($rule, $option) = $item;
			$action = $array[0];
			if (isset($option['allow']) && !in_array($action, explode(",", $option['allow']))) {
				return false;
			} elseif (isset($option['except']) && in_array($action, explode(",", $option['except']))) {
				return false;
			}
			if (isset($option['method'][$action])) {
				$option['method'] = $option['method'][$action];
			}
		} else {
			$rule = $item;
		}
		if (isset($option) && !self::checkOption($option, $request)) {
			return false;
		}
	}

	/**
     * 路由参数有效性检查
     * @param array     $option 路由参数
     * @param Request   $request Request对象
     * @return bool
     */
    public static function checkOption($option, $request)
    {
        if ((isset($option['method']) && is_string($option['method']) && false === stripos($option['method'], $request->method()))
            || (isset($option['ajax']) && $option['ajax'] && !$request->isAjax()) // Ajax检测
             || (isset($option['ajax']) && !$option['ajax'] && $request->isAjax()) // 非Ajax检测
             || (isset($option['pjax']) && $option['pjax'] && !$request->isPjax()) // Pjax检测
             || (isset($option['pjax']) && !$option['pjax'] && $request->isPjax()) // 非Pjax检测
             || (isset($option['ext']) && false === stripos('|' . $option['ext'] . '|', '|' . $request->ext() . '|')) // 伪静态后缀检测
             || (isset($option['deny_ext']) && false !== stripos('|' . $option['deny_ext'] . '|', '|' . $request->ext() . '|'))
            || (isset($option['domain']) && !in_array($option['domain'], [$_SERVER['HTTP_HOST'], self::$subDomain])) // 域名检测
             || (isset($option['https']) && $option['https'] && !$request->isSsl()) // https检测
             || (isset($option['https']) && !$option['https'] && $request->isSsl()) // https检测
             || (!empty($option['before_behavior']) && false === Hook::exec($option['before_behavior'])) // 行为检测
             || (!empty($option['callback']) && is_callable($option['callback']) && false === call_user_func($option['callback'])) // 自定义检测
        ) {
            return false;
        }
        return true;
    }

    /**
     * 检测路由规则
     * @param string    $rule 路由规则
     * @param string    $route 路由地址
     * @param string    $url URL地址
     * @param array     $patten 变量规则
     * @param array     $option 路由参数
     * @param string    $depr URL分隔符（全局）
     * @return array|false
     */
    protected static function checkRule($rule, $route, $url, $patten, $option, $depr){
        // 检查完整规则定义
        if (isset($patten['__url__']) && !preg_match(0 === strpos($patten['__url__'], '/') ? $patten['__url__'] : '/^' . $patten['__url__'] . '/', str_replace('|', $depr, $url))) {
            return false;
        }
        // 检查路由的参数分隔符
        if (isset($option['param_depr'])) {
            $url = str_replace(['|', $option['param_depr']], [$depr, '|'], $url);
        }

        $len1 = substr_count($url, '|');
        $len2 = substr_count($rule, '/');
        // 多余参数是否合并
        $merge = !empty($option['merge_extra_vars']);
        if ($merge && $len1 > $len2) {
            $url = str_replace('|', $depr, $url);
            $url = implode('|', explode($depr, $url, $len2 + 1));
        }

        if ($len1 >= $len2 || strpos($rule, '[')) {
            if (!empty($option['complete_match'])) {
                // 完整匹配
                if (!$merge && $len1 != $len2 && (false === strpos($rule, '[') || $len1 > $len2 || $len1 < $len2 - substr_count($rule, '['))) {
                    return false;
                }
            }
            $patten = array_merge(self::$rules['patten'], $patten);
            if (false !== $match = self::match($url, $rule, $patten)) {
                // 匹配到路由规则
                return self::parseRule($rule, $route, $url, $option, $match);
            }
        }
        return false;
    }

    /**
     * 解析规则路由
     * @param string    $rule 路由规则
     * @param string    $route 路由地址
     * @param string    $pathinfo URL地址
     * @param array     $option 路由参数
     * @param array     $matches 匹配的变量
     * @return array
     * @todo hook
     */
    public static function parseRule($rule, $route, $pathinfo, $option = [], $matches = []) {

        $request = Request::instance();
        // 解析路由规则
        if ($rule) {
            $rule = explode('/', $rule);
            // 获取URL地址中的参数
            $paths = explode('|', $pathinfo);
            foreach ($rule as $item) {
                $fun = '';
                if (0 === strpos($item, '[:')) {
                    $item = substr($item, 1, -1);
                }
                if (0 === strpos($item, ':')) {
                    $var           = substr($item, 1);
                    $matches[$var] = array_shift($paths);
                } else {
                    // 过滤URL中的静态变量
                    array_shift($paths);
                }
            }
        } else {
            $paths = explode('|', $pathinfo);
        }

        // 获取路由地址规则
        if (is_string($route) && isset($option['prefix'])) {
            // 路由地址前缀
            $route = $option['prefix'] . $route;
        }
        // 替换路由地址中的变量
        if (is_string($route) && !empty($matches)) {
            foreach ($matches as $key => $val) {
                if (false !== strpos($route, ':' . $key)) {
                    $route = str_replace(':' . $key, $val, $route);
                }
            }
        }

        // 解析额外参数
        self::parseUrlParams(empty($paths) ? '' : implode('|', $paths), $matches);
        // 记录匹配的路由信息
        $request->routeInfo(['rule' => $rule, 'route' => $route, 'option' => $option, 'var' => $matches]);

        // 检测路由after行为
        if (!empty($option['after_behavior'])) {
            if ($option['after_behavior'] instanceof \Closure) {
                $result = call_user_func_array($option['after_behavior'], []);
            } else {
                foreach ((array) $option['after_behavior'] as $behavior) {
                    if (!is_null($result)) {
                        break;
                    }
                }
            }
            // 路由规则重定向
            if ($result instanceof Response) {
                return ['type' => 'response', 'response' => $result];
            } elseif (is_array($result)) {
                return $result;
            }
        }

        if ($route instanceof \Closure) {
            // 执行闭包
            $result = ['type' => 'function', 'function' => $route];
        } elseif (0 === strpos($route, '/') || strpos($route, '://')) {
            // 路由到重定向地址
            $result = ['type' => 'redirect', 'url' => $route, 'status' => isset($option['status']) ? $option['status'] : 301];
        } elseif (false !== strpos($route, '\\')) {
            // 路由到方法
            list($path, $var) = self::parseUrlPath($route);
            $route            = str_replace('/', '@', implode('/', $path));
            $method           = strpos($route, '@') ? explode('@', $route) : $route;
            $result           = ['type' => 'method', 'method' => $method, 'var' => $var];
        } else {
            $result = self::parseApplication($route);
        }
        return $result;
    }

    /**
     * 解析URL地址为 控制器/操作
     * @access private
     * @param string    $url URL地址
     * @return array
     */
    public static function parseApplication($url)
    {
        list($path, $var) = self::parseUrlPath($url);
        $action           = array_pop($path);
        $controller       = !empty($path) ? array_pop($path) : null;
        $method           = Request::instance()->method();
        // 设置当前请求的路由变量
        Request::instance()->route($var);
        Request::instance()->controller($controller)->action($action);
        // 路由到/控制器/操作
        return ['type' => 'application', 'application' => [$controller, $action], 'convert' => false];
    }

	/**
     * 解析模块的URL地址 [模块/控制器/操作?]参数1=值1&参数2=值2...
     * @access public
     * @param string    $url URL地址
     * @param string    $depr URL分隔符
     * @param bool      $autoSearch 是否自动深度搜索控制器
     * @return array
     */
    public static function parseUrl($url, $depr = '/'){
        $url              = str_replace($depr, '|', $url);
        list($path, $var) = self::parseUrlPath($url);
        $route            = [null,null, null];
        if (isset($path)) {
			//获取控制器目录
			$controller_path = CONTROLLER_PATH;
			//获取控制器后缀
            $suffix = App::$suffix ? App::$suffix :  ucfirst(strtolower(Config::get('controller_suffix')));
            $item   = [];
            $find   = false;
            foreach ($path as $val) {
                $item[] = $val;
                $file   = $controller_path . ucfirst(strtolower($val)) . $suffix . EXT;
                if (is_file($file)) {
                    $find = true;
                    break;
                }
            }
            if ($find) {
                $controller = implode('.', $item);
                $path       = array_slice($path, count($item));
            } else {
                $controller = array_shift($path);
            }
        } else {
            // 解析控制器
            $controller = !empty($path) ? array_shift($path) : null;
        }
        // 解析操作
        $action = !empty($path) ? array_shift($path) : null;
        // 解析额外参数
        self::parseUrlParams(empty($path) ? '' : implode('|', $path));
        // 封装路由
        $route = [$controller, $action];
        Request::instance()->controller($controller)->action($action);
        return ['type'=> 'application', 'application' => $route];
    }

    /**
     * 解析URL的pathinfo参数和变量
     * @access private
     * @param string    $url URL地址
     * @return array
     */
    public static function parseUrlPath($url) {
        // 分隔符替换 确保路由定义使用统一的分隔符
        $url = str_replace('|', '/', $url);
        $url = trim($url, '/');
        $var = [];
        if (false !== strpos($url, '?')) {
            // [控制器/操作?]参数1=值1&参数2=值2...
            $info = parse_url($url);
            $path = explode('/', $info['path']);
            parse_str($info['query'], $var);
        } elseif (strpos($url, '/')) {
            // [控制器/操作]
            $path = explode('/', $url);
        } else {
            $path = [$url];
        }
        return [$path, $var];
    }

    /**
     * 解析URL地址中的参数Request对象
     * @access private
     * @param string    $url 路由规则
     * @param array     $var 变量
     * @return void
     */
    private static function parseUrlParams($url, &$var = []) {
        if ($url) {
            preg_replace_callback('/(\w+)\|([^\|]+)/', function ($match) use (&$var) {
                $var[$match[1]] = strip_tags($match[2]);
            }, $url);
        }
        // 设置当前请求的参数
        Request::instance()->route($var);
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
			if (false !== strpos($value, "<") && preg_match_all('/<(\w+(\??))>/', $value, $matches)) {
				foreach ($matches[1] as $name) {
					if (strpos($name, "?")) {
						$name = substr($name, 0, -1);
						$optional = true;
					} else {
						$optional = false;
					}
					$var[$name] = $optional ? 2 : 1;
				}
			}
	        if (0 === strpos($value, '[:')) {
	            // 可选参数
	            $optional = true;
	            $value = substr($value, 1, -1);
	        }
	        if (0 === strpos($value, ':')) {
	            // URL变量
	            $name = substr($value, 1);
	            $var[$name] = $optional ? 2 : 1;
	        }
	    }
	    return $var;
	}

    /**
     * 检测URL和规则路由是否匹配
     * @access private
     * @param string    $url URL地址
     * @param string    $rule 路由规则
     * @param array     $patten 变量规则
     * @return array|false
     */
    private static function match($url, $rule, $patten) {
        $m2 = explode('/', $rule);
        $m1 = explode('|', $url);

        $var = [];
        foreach ($m2 as $key => $val) {
            // val中定义了多个变量 <id><name>
            if (false !== strpos($val, '<') && preg_match_all('/<(\w+(\??))>/', $val, $matches)) {
                $value   = [];
                $replace = [];
                foreach ($matches[1] as $name) {
                    if (strpos($name, '?')) {
                        $name      = substr($name, 0, -1);
                        $replace[] = '(' . (isset($patten[$name]) ? $patten[$name] : '\w+') . ')?';
                    } else {
                        $replace[] = '(' . (isset($patten[$name]) ? $patten[$name] : '\w+') . ')';
                    }
                    $value[] = $name;
                }
                $val = str_replace($matches[0], $replace, $val);
                if (preg_match('/^' . $val . '$/', isset($m1[$key]) ? $m1[$key] : '', $match)) {
                    array_shift($match);
                    foreach ($value as $k => $name) {
                        if (isset($match[$k])) {
                            $var[$name] = $match[$k];
                        }
                    }
                    continue;
                } else {
                    return false;
                }
            }

            if (0 === strpos($val, '[:')) {
                // 可选参数
                $val      = substr($val, 1, -1);
                $optional = true;
            } else {
                $optional = false;
            }
            if (0 === strpos($val, ':')) {
                // URL变量
                $name = substr($val, 1);
                if (!$optional && !isset($m1[$key])) {
                    return false;
                }
                if (isset($m1[$key]) && isset($patten[$name])) {
                    // 检查变量规则
                    if ($patten[$name] instanceof \Closure) {
                        $result = call_user_func_array($patten[$name], [$m1[$key]]);
                        if (false === $result) {
                            return false;
                        }
                    } elseif (!preg_match(0 === strpos($patten[$name], '/') ? $patten[$name] : '/^' . $patten[$name] . '$/', $m1[$key])) {
                        return false;
                    }
                }
                $var[$name] = isset($m1[$key]) ? $m1[$key] : '';
            } elseif (!isset($m1[$key]) || 0 !== strcasecmp($val, $m1[$key])) {
                return false;
            }
        }
        // 成功匹配后返回URL中的动态变量数组
        return $var;
    }

}
