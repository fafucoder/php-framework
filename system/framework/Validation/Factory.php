<?php
namespace Framework\Validation;

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
}