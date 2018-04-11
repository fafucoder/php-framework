<?php
namespace System;

class Session {

	public static $init = null;

	/**
	 * session初始化
	 * @param  array  $config config配置
	 * @return void
	 */
	public static function init(array $config = []) {
		if (empty($config)) {
			$config = Config::get('session');
		}
		$start = false;
		if (!empty($config['auto_start']) && PHP_SESSION_ACTIVE != session_status()) {
            ini_set('session.auto_start', 0);
            $start = true;
        }

		// 启动session
		if (isset($config['session_id']) && isset($_REQUEST[$config['session_id']])) {
			session_id($_REQUEST[$config['session_id']]);
		} elseif (isset($config['id']) && !empty($config['id'])) {
			session_id($config['id']);
		}
		if (isset($config['name'])) {
			session_name($config['name']);
		}
		if (isset($config['path'])) {
			session_save_path($config['path']);
		}
		if (isset($config['domain'])) {
			ini_set('session.cookie_domain', $config['domain']);
		}
		if (isset($config['expire'])) {
			ini_set('session.gc_maxlifetime', $config['expire']);
			ini_set('session.cookie_lifetime', $config['expire']);
		}
		if (isset($config['secure'])) {
			ini_set('session.cookie_secure', $config['secure']);
		}
		if (isset($config['use_cookies'])) {
			ini_set('session.use_cookies', $config['use_cookies'] ? 1 : 0);
		}
		//会话开始
		if ($start) {
            session_start();
            self::$init = true;
        } else {
            self::$init = false;
        }
		//@todo session type
	}

	/**
     * session自动启动或者初始化
     * @return void
     */
    public static function boot() {
        if (is_null(self::$init)) {
            self::init();
        } elseif (false === self::$init) {
            if (PHP_SESSION_ACTIVE != session_status()) {
                session_start();
            }
            self::$init = true;
        }
    }

	/**
	 * session设置
	 * @param string $name  session name
	 * @param string $value session value
	 */
	public static function set($name, $value = '') {
		empty(self::$init) && self::boot();
		if (strpos(".", $name)) {
			list($name1, $name2) = explode('.', $name);
			$_SESSION[$name1][$name2] = $value;
		} else {
			$_SESSION[$name] = $value;
		}
	}

	/**
	 * session获取
	 * @param  string $name session name
	 * @return mixed
	 */
	public function get($name = '') {
		empty(self::$init) && self::boot();
		if ('' == $name) {
			return $_SESSION;
		} elseif (strpos(".", $name)) {
			list($name1, $name2) = explode(".", $name);
			return isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
		} else {
			return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
		}
	}

	/**
	 * 是否有session
	 * @param  string  $name session name
	 * @return boolean
	 */
	public static function has($name = '') {
		empty(self::$init) && self::boot();
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            return isset($_SESSION[$name1][$name2]):
        } else {
            return isset($_SESSION[$name]);
        }
	}

	/**
	 * 删除session
	 * @param  string $name session name
	 * @return void
	 */
	public static function delete($name = '') {
		empty(self::$init) && self::boot();
        if (is_array($name)) {
            foreach ($name as $n) {
                self::delete($n);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            unset($_SESSION[$name1][$name2]);
        } else {
            unset($_SESSION[$name]);
        }
	}

	/**
     * 清空session
     * @return void
     */
    public static function clear() {
        empty(self::$init) && self::boot();
        $_SESSION = [];
    }

	/**
     * 启动session
     * @return void
     */
    public static function start() {
        session_start();
        self::$init = true;
    }

	/**
	 * 销毁session
	 * @return void
	 */
	public static function destroy() {
		if (!empty($_SESSION)) {
			$_SESSION = [];
		}
		session_unset();
		session_destroy();
		self::$init = null;
	}
}
