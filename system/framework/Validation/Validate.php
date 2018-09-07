<?php
namespace Framework\Validation;

class Validate extends Factory {

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
	 * @param  array  $rules
	 * @return mixed          
	 */
	public function validate($data, $rules = array()) {
		if (empty($rules)) {
			$rules = $this->rules;
		}

		foreach ($rules as $key => $fieldRules) {
			$value = static::getDataValueByKey($data, $key);
			$result = $this->checkField($key, $value, $fieldRules);
		}
	}

	/**
	 * Get the data value by key.
	 * 
	 * @param  array $data  data
	 * @param  string $key  data key
	 * @return mixed
	 */
	public static function getDataValueByKey($data, $key) {
		if (array_key_exists($key, $data)) {
			return $data[$key];
		}
		return null;
	}

	/**
	 * Validate field.
	 * 
	 * @param  string $field      field name
	 * @param  mixed $value       field value
	 * @param  array $fieldRules  field rule
	 * @return mixed             
	 */
	public function checkField($field, $value, $fieldRules) {
		if (is_string($fieldRules)) {
			$rule = $fieldRules;
			$params = array();
			$result = $this->checkRule($field, $value, $rule, $params);
		} elseif (is_array($fieldRules)) {
			foreach ($fieldRules as $rules => $params) {
				if (is_numeric($rules)) {
					$rules = explode("|", $params);
					$params = array();
					foreach ($rules as $rule) {
						$result = $this->checkRule($field, $value, $rule, $params);
					}
				}

				$result = $this->checkRule($field, $value, $rule, $params);
			}
		}

	}

	/**
	 * Check the field rule is valid.
	 * 
	 * @param  string $field  field name
	 * @param  mixed $value  field value
	 * @param  string $rule   rule name
	 * @param  mixed $params field params
	 * 
	 * @return  mixed
	 */
	public function checkRule($field, $value, $rule, $params) {
		if ($params instanceof \Closure) {
			$result = call_user_func_array($params, array($field, $value));
		} else {
			$result = call_user_func_array(__CLASS__ . :: . $rules, array($value, $params, $field));
		}

		if (false === $result) {
			$message = $this->getRuleErrorMessage($field, $rule, $params);

			if (null !== $message) {
				$this->errors[$field][$rule] = $message;
			}
		}

		return $result;
	}
}