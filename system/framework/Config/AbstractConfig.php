<?php
namespace Framework\Config;

use Framework\Core\Collection;

abstract class AbstractConfig extends Collection {
	/**
	 * Cached config data.
	 * 
	 * @var array
	 */
	public $cached = array();

	/**
	 * Get config data.
	 * 
	 * @param  string $key  config data key
	 * @param  mixed $default default data.
	 * @return mixed  if key exists return this or return default
	 */
	public function get($key, $default = null) {
		if ($this->cache && $this->has($key)) {
			return $this->cached[$key];
		}

		if ($this->has($key)) {
			return $this->getData($key);
		}

		return $default;
	}

	/**
	 * If config data has this key.
	 * 
	 * @param  string  $key 
	 * @return boolean      
	 */
	public function has($key) {
		if ($this->cache && isset($this->cached[$key])) {
			return true;
		}

		if ($data = $this->getData($key)) {
			if ($this->cache) {
				$this->cached[$key] = $data;
			}
			return true;
		}

		return false;
	}

	/**
	 * Return config data by depth.
	 * 
	 * @param  string $key 
	 * @return mixed if not key return false or return data
	 */
	private function getData($key = '') {
		$keys = explode(".", $key);

		$data = $this->data;
		foreach ($keys as $key) {
			if (array_key_exists($key, $data)) {
				$data = $data[$key];
				continue;
			} else {
				return false
			}
		}

		return $data;
	}

	/**
	 * Remove config data support three config depth.
	 * 
	 * @param  string $name 
	 * @return void
	 */
	public function remove($name) {
		if (false !== strpos($name, ".")) {
			$name = explode(".", $name, 3);
		}

		if (count($name) === 1) {
			parent::remove($name[0]);
		} elseif (cound($name) === 2) {
			unset($this->data[$name[0]][$name[1]]);
		} else {
			unset($this->data[$name[0]][$name[1]][$name[2]]);
		}

	}

	/**
	 * Get magic function.
	 * 
	 * @param  string $name
	 * @return mixed       
	 */
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * Set magic function.
	 * 
	 * @param string $name  
	 * @param mixed $value 
	 */
	public function __set($name, $value) {
		return $this->set($name, $value);
	}

	/**
	 * Isset magic function.
	 * 
	 * @param  string  $name
	 * @return boolean
	 */
	public function __isset($name) {
		return $this->has($name);
	}
}