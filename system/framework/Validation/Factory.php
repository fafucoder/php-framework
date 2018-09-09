<?php
namespace Framework\Validation;

use Framework\Validation\Validater\ValidaterInterface;
use Framework\Validation\Exceptions\ValidaterNotFoundException;

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
	public static $alias = array();

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
	 * Register validater alias.
	 * 
	 * @param  string|array $validater  validater name
	 * @param  string $alias     alias name
	 * @return void
	 */
	public function registerValidateAlias($validater, $alias) {
		if (is_array($validater)) {
			static::$alias = array_merge(static::$alias, $validater);
		} else {
			static::$alias[$validater] = $alias;
		}
	}

	/**
	 * Set validater prefix.
	 * 
	 * @param string $prefix 
	 */
	public function setValidaterPrefix($prefix) {
		$this->validatePrefixs = $prefix;
	}

	/**
	 * Get validater prefix.
	 * 
	 * @return string 
	 */
	public function getValidaterPrefix() {
		return $this->validatePrefixs;
	}

	/**
	 * Validate filed rule.
	 * 
	 * @param  string $field  field name
	 * @param  mixed $value  field value
	 * @param  string $rule   rule name
	 * @param  mixed $params field params
	 * 
	 * @return mixed         
	 */
	public function validateRule($field, $value, $rule, $params) {
		if (array_key_exists($rule, static::$alias)) {
			$rule = static::$alias[$rule];
		}
		
		if ($validater = $this->checkValidater($rule)) {
			return $validater::validate($value, $params);
		}

		throw new ValidaterNotFoundException(sprintf("Rule %s not found for %s!"), $rule, $field);
	}

	/**
	 * Get field error message.
	 * 
	 * @param  string $field  field name
	 * @param  array  $params params
	 * 
	 * @return mixed may be is string or null      
	 */
	public function getErrors($field, $rule, $params = array()) {
		if ($validater = $this->checkValidater($field)) {
			return $validater::errors($field, $params);
		}
	}

	/**
	 * __callStatic magic function.
	 * 
	 * @param  string $method method name
	 * @param  array $params method params.
	 * 
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
	 * 
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
	 * 
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