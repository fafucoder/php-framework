<?php 
namespace System;

class Url {
	public static $root;

    /**
     * 生成url
     * @param  string  $url    url
     * @param  string  $vars   参数
     * @param  boolean $suffix 是否伪静态后缀
     * @param  boolean $domain 是否生成域名   
     * @return string
     */
	public static function build($url = '', $vars = '', $suffix = true) {
		if (0 === strpos($url, '[') && $pos = strpos($url, ']')) {
			$name = substr($url, 1, $pos - 1);
			$url = 'name' . substr($url, $pos + 1);
		}
		if (false === strpos($url, "://") && 0 !== strpos($url, "/")) {
			$info = parse_url($url);
			$url = !empty($info['path']) ? $info['path'] : "";
			if (isset($info['fragment'])) {
				$anchor = $info['fragment'];
				if (false !== strpos($anchor, "?")) {
					list($anchor, $info['query']) = explode("?", $anchor, 2);
				}
				if (false !== strpos($anchor, '@')) {
					list($anchor, $domain) = explode('@', $anchor, 2);
				}
			} elseif (strpos($url, "@") && false === strpos($url, '\\')) {
				list($url, $domain) = explode('@', $url, 2);
			}
		}
		if (is_string($vars)) {
			parse_str($vars, $vars);
		}

		if ($url) {
			$rule = Route::name(isset($name) ? $name : $url . (isset($info['query']) ? "?" . $info['query'] : ""));
			if (is_null($rule) && isset($info['query'])) {
				$rule = Route::name($url);
				// 解析地址里面参数 合并到vars
                parse_str($info['query'], $params);
                $vars = array_merge($params, $vars);
                unset($info['query']);
			}
		}

		if (!empty($rule) && $match = self::getRuleUrl($rule, $vars)) {
			// 匹配路由命名标识
            $url = $match[0];
            // 替换可选分隔符
            $url = preg_replace(['/(\W)\?$/', '/(\W)\?/'], ['', '\1'], $url);
            if (!is_null($match[1])) {
                $suffix = $match[1];
            } 
		} elseif (!empty($rule) && isset($name)) {
            throw new \Exception('route name not exists:' . $name);
        } else {
        	// 检查别名路由
            $alias      = Route::rules('alias');
            $matchAlias = false;
            if ($alias) {
                // 别名路由解析
                foreach ($alias as $key => $val) {
                    if (is_array($val)) {
                        $val = $val[0];
                    }
                    if (0 === strpos($url, $val)) {
                        $url        = $key . substr($url, strlen($val));
                        $matchAlias = true;
                        break;
                    }
                }
            }
            if (!$matchAlias) {
                // 路由标识不存在 直接解析
                $url = self::parseUrl($url);
            }
            if (isset($info['query'])) {
                // 解析地址里面参数 合并到vars
                parse_str($info['query'], $params);
                $vars = array_merge($params, $vars);
            }
        }

        // 还原URL分隔符
        $depr = Config::get('pathinfo_depr');
        $url  = str_replace('/', $depr, $url);
        // URL后缀
        $suffix = in_array($url, ['/', '']) ? '' : self::parseSuffix($suffix);
         // 锚点
        $anchor = !empty($anchor) ? '#' . $anchor : '';
        // 参数组装
        if (!empty($vars)) {
            foreach ($vars as $var => $val) {
                if ('' !== trim($val)) {
                    $url .= $depr . $var . $depr . urlencode($val);
                }
            }
            $url .= $suffix . $anchor;
        } else {
            $url .= $suffix . $anchor;
        }
        // URL组装
        $url = rtrim(self::$root ?: Request::instance()->root(), '/') . '/' . ltrim($url, '/');
        return $url;
	}

    /**
     * 解析路由地址
     * @param  string $url     路由地址
     * @return string
     */
    protected static function parseUrl($url)
    {
        $request = Request::instance();
        if (0 === strpos($url, '/')) {
            // 直接作为路由地址解析
            $url = substr($url, 1);
        } elseif (false !== strpos($url, '\\')) {
            // 解析到类
            $url = ltrim(str_replace('\\', '/', $url), '/');
        } elseif (0 === strpos($url, '@')) {
            // 解析到控制器
            $url = substr($url, 1);
        } else {
            $controller = $request->controller();
            if ('' == $url) {
                // 空字符串输出当前的 控制器/操作
                $action = $request->action();
            } else {
                $path       = explode('/', $url);
                $action     = array_pop($path);
                $controller = empty($path) ? $controller : array_pop($path);

            }
            $url = $controller . '/' . $action;
        }
        return $url;
    }

    // 解析URL后缀
    public static function parseSuffix($suffix)
    {
        if ($suffix) {
            $suffix = true === $suffix ? Config::get('default_return_type') : $suffix;
            if ($pos = strpos($suffix, '|')) {
                $suffix = substr($suffix, 0, $pos);
            }
        }
        return (empty($suffix) || 0 === strpos($suffix, '.')) ? $suffix : '.' . $suffix;
    }


	/**
	 * 获取路由地址
	 * @param  string|array $rule  路由规则
	 * @param  array  &$vars 变量
	 * @return mixed        
	 */
	public static function getRuleUrl($rule, &$vars = []) {
		foreach ($rule as $item) {
			list($url, $patten, $suffix) = $item;
			if (empty($patten)) {
				return [$url, $suffix];
			}
			foreach ($patten as $key => $value) {
				if (isset($vars[$key])) {
					$url = str_replace(['[:' . $key . ']', '<' . $key . '?>', ':' . $key . '', '<' . $key . '>'], $type ? $vars[$key] : urlencode($vars[$key]), $url);
					unset($vars[$key]);
					$result = [$url, $suffix];
				} elseif (2 == $values) {
					 $url    = str_replace(['/[:' . $key . ']', '[:' . $key . ']', '<' . $key . '?>'], '', $url);
                    $result = [$url, $suffix];
				} else {
					break;
				}
			}
			if (isset($result)) {
				return $result;
			}
		}
		return false;
	}

	/**
	 * 制定根地址
	 * @param  string $root 根地址
	 * @return void       
	 */
	public static function root($root) {
		self::$root = $root;
		Request::instance()->root($root);
	}
}