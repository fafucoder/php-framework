<?php
namespace Framework\Validate;

class Validate {
	/**
	 * The custom rules.
	 * 
	 * @var array
	 */
	public static $customRules = array();

	/**
	 * The custom messages.
	 * 
	 * @var array
	 */
	public static $customMessages = array();

	/**
	 * The default messages.
	 * 
	 * @var array
	 */
	public static $messages = array();

	/**
	 * The default rules.
	 * 
	 * @var array
	 */
	public static $rules = array();

	/**
	 * fields error information.
	 * 
	 * @var array
	 */
	public static $errors = array();

	/**
	 * Validate fileds.
	 * 
	 * @var array
	 */
	protected $fileds = array();

	/**
	 * The validate type alias.
	 * 
	 * @var array
	 */
	protected static $alias = array();

	/**
	 * Construct.
	 * 
	 * @param array $options        [description]
	 * @param array $customMessages [description]
	 */
	public function __construct(array $options = array(), array $customMessages = array()) {

	}

	/**
	 * Validate fileds rule.
	 * 
	 * @param  array $data    
	 * @param  array  $options 
	 * @return mixed          
	 */
	public function validate($data, $options = array()) {

	}

	/**
	 * Remove a rule for given field.
	 * 
	 * @param  string  $field 
	 * @param  mixed $rule  
	 * @return void         
	 */
	public function remove($field, $rule = true) {

	}

	/**
	 * Append a rule for given filed.
	 * 
	 * @param  string $field 
	 * @param  mixed $rule  
	 * @return void        
	 */
	public function append($field, $rule = null) {

	}

	/**
	 * __callStatic magic function.
	 * 
	 * @param  string $method  $method name
	 * @param  mixed $args   method arguments
	 * @return mixed
	 */
	public function __callStatic($method, $args) {

	}

}