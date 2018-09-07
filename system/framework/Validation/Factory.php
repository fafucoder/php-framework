<?php
namespace Framework\Validation;

use Framework\Validation\Validater\ValidaterInterface;

class Factory {
	/**
	 * Validater class prefix.
	 * 
	 * @var string
	 */
	public $validatePrefixs = 'Framework\\Validation\\Validater\\';

	/**
	 * Custom validater.
	 * 
	 * @var array
	 */
	public static $customValidater = array();

	/**
	 * Custom validater errors.
	 * 
	 * @var array
	 */
	public static $customErrors = array();

	/**
	 * The validate type alias.
	 * 
	 * @var array
	 */
	protected static $alias = array();

	/**
	 * Register custom validater.
	 * 
	 * @param  mixed $validater custom validater
	 * @param  mixed $callable  
	 * @param  mixed $errors    
	 * @return void            
	 */
	public function registerValidater($validater, $callable, $errors = null) {
		if (is_callable($callable)) {
			static::$customValidater[$validater] = $callable;

			if ($errors) {
				static::$customErrors[$validater] = $errors;
			}
		}
	}

	/**
	 * Register custom validater error message.
	 * 
	 * @param  string|array $validater 
	 * @param  string $errors    
	 * @return void            
	 */
	public function registerValidaterErrors($validater, $errors = null) {
		if (is_array($validater)) {
			static::$customErrors = array_merge(static::$customErrors, $validater);
		} else {
			static::$customErrors[$validater] = $errors;
		}
	}

	/**
	 * Get field error message.
	 * 
	 * @param  string $field  field name
	 * @param  mixed $value  filed value.
	 * @param  array  $params params
	 * @return mixed may be is string or null      
	 */
	public function getErrorMessage($field, $value, $params = array()) {

	}

	/**
	 * __callStatic magic function.
	 * 
	 * @param  string $method method name
	 * @param  array $params method params.
	 * @return mixed         
	 */
	public function __callStatic($method, $params) {
		if (array_key_exists($method, static::$customValidater)) {
			return call_user_func_array(static::$customValidater[$method], $params);
		} elseif ($validater = static::checkValidater($method)) {
			$value = isset($params[0]) ? $params[0] : null;
			$options = isset($params[1]) ? $params[1] : array();
			return $validater::validate($value, $options);
		}

		throw new \BadMethodCallException('static method not exists ' . __CLASS__ . '::' . $method . '()');
	}

	/**
	 * __call magic function.
	 * 
	 * @param  string $method method name 
	 * @param  array $params  method param
	 * @return mixed
	 */
	public function __call($method, $params) {
		if (array_key_exists($method, static::$customValidater)) {
			return call_user_func_array(static::$customValidater[$method], $params);
		} elseif ($validater = static::checkValidater($method)) {
			$value = isset($params[0]) ? $params[0] : null;
			$options = isset($params[1]) ? $params[1] : array();
			return $validater::validate($value, $options);
		}

		throw new \BadMethodCallException('method not exists ' . __CLASS__ . '->' . $method . '()');
		
	}

	/**
	 * Check validater class exists.
	 * @param  string $method
	 * 
	 * @return string|false if calss exist return class or retunr false
	 */
	protected static function checkValidater($method) {
		$validater = $this->validatePrefixs . ucfirst(strtolower($method));

		if (class_exists($validater)) {
			return $validater;
		}

		return false;
	}
}