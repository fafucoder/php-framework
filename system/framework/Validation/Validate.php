<?php
namespace Framework\Validation;

class Validate extends Factory {

	/**
	 * The default messages.
	 * 
	 * @var array
	 */
	public $messages = array();

	/**
	 * fields error information.
	 * 
	 * @var array
	 */
	public $errors = array();

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
	 * @param array $messages
	 */
	public function __construct(array $rules = array(), array $messages = array()) {
		$this->rules = array_merge($this->rules, $rules);
		$this->messages = array_merge($this->messages, $messages);
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

		foreach ($rules as $field => $fieldRules) {
			$value = $this->getFieldDataValue($data, $field);
			$result = $this->checkField($field, $value, $fieldRules);

			if (true !== $result) {
				$this->errors
			}
		}
	}

	/**
	 * Get the data value by key.
	 * 
	 * @param  array $data  data
	 * @param  string $field  data key
	 * @return mixed
	 */
	public function getFieldDataValue($data, $field) {
		if (array_key_exists($field, $data)) {
			return $data[$field];
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
			$fieldRules = (array) $fieldRules;
		}

		if (array_key_exists($field, $this->appends)) {
			$fieldRules = array_merge($fieldRules, $this->appends[$fields]);
		}

		foreach ($fieldRules as $rules => $params) {
			if (is_numeric($rules)) {
				$fieldRules = explode("|", $fieldRules);

				foreach ($fieldRules as $key => $rule) {
					if (false !== strpos($rule, "(")) {
						list($rule, $params) = explode("(", rtrim($rule, ")"));
						$params = explode(",", $params);
					} else {
						$params = array();
					}

					//if in remove rules continue
					if (array_key_exists($field, $this->removes) && in_array($rule, $this->removes[$field])) {
						continue;
					}

					$result = $this->checkRule($field, $value, $rule, $params);
				}
			}

			//if in remove rules continue
			if (array_key_exists($field, $this->removes) && in_array($rule, $this->removes[$field])) {
				continue;
			}
			$result = $this->checkRule($field, $value, $rule, $params);
		}

		return isset($this->errors[$field]) && !empty($this->errors[$field]) ? $this->errors[$field] : true;
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

			$result = $this->validateRule($field, $value, $rule, $params);
		}

		if (false === $result) {
			$message = $this->getErrorMessage($field, $rule, $params);

			if (null !== $message) {
				$this->errors[$field][$rule] = $message;
			}
		}

		return $result;
	}

	/**
	 * Get error messages.
	 * 
	 * @param  string $field   field name
	 * @param  string $rule    rule 
	 * @param  array  $params  filed params
	 * @return string|null
	 */
	public function getErrorMessage($field, $rule, $params) {
		if (isset($this->messages[$field][$rule])) {
			return $this->messages[$field][$rule];
		}

		return $this->getErrors($field, $rule, $params);
	}
}