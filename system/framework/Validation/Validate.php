<?php
namespace Framework\Validate;

class Validate {

	/**
	 * The default messages.
	 * 
	 * @var array
	 */
	public static $messages = array();

	/**
	 * fields error information.
	 * 
	 * @var array
	 */
	public static $errors = array();

	/**
	 * Validate fields.
	 * 
	 * @var array
	 */
	protected $fields = array();

	/**
	 * The default rules.
	 * 
	 * @var array
	 */
	protected $rules = array();

	/**
	 * The append field rules.
	 * 
	 * @var array
	 */
	protected $appends = array();

	/**
	 * The remove field rules.
	 * 
	 * @var array
	 */
	protected $removes = array();

	/**
	 * Construct.
	 * 
	 * @param array $rules
	 * @param array $customMessages
	 */
	public function __construct(array $rules = array(), array $customMessages = array()) {
		$this->rules = array_merge($this->rules, $rules);
		static::$customMessages = array_merge(static::$customMessages, $customMessages);
	}

	/**
	 * Remove a rule for given field.
	 * 
	 * @param  string  $field 
	 * @param  mixed $rule  
	 * @return $this    
	 */
	public function removeRule($field, $rule = '') {
		if (is_array($field)) {
			foreach ($field as $f => $rule) {
				$this->removeRule($f, $rule);
			}
		} else {
			if (is_string($rule)) {
				$rule = explode("|", $rule);
			}

			$this->removes[$field] = $rule;
		}

		return $this;
	}

	/**
	 * Append a rule for given field.
	 * 
	 * @param  string $field 
	 * @param  mixed $rule  
	 * @return $this      
	 */
	public function appendRule($field, $rule = null) {
		if (is_array($field)) {
			foreach ($field as $f => $rule) {
				$this->appendRule($f, $rule);
			}
		} else {
			if (is_string($rule)) {
				$rule = explode("|", $rule);
			}

			$this->append[$field] = $rule;
		}

		return $this;
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
	 * __callStatic magic function.
	 * 
	 * @param  string $method  $method name
	 * @param  mixed $args   method arguments
	 * @return mixed
	 */
	public function __callStatic($method, $args) {

	}
}