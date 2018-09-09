<?php
namespace Framework\Validation\Validater;

class Number extends ValidaterInterface {
	/**
	 * validate functions.
	 * 
	 * @param  mixed $value   validate value
	 * @param  mixed  $options validate options
	 * @return boolean
	 */
	static public function validate($value, $options = array()) {
		return is_numeric($value);
	}

	/**
	 * Return error message.
	 * 
	 * @param  string $field   field name
	 * @param  array  $options field params
	 * @return string | null          
	 */
	static public function errors($field, $options = array()) {
		return sprintf("The %s is not number", $field);
	}
}