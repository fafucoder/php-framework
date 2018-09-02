<?php
namespace Framework\Config\Parser;

use Framework\Config\Exception\ParseException;

class Ini implements ParserInterface {
	/**
	 * Parses a file from path and get its contents as an array.
	 * 
	 * @param  string $path 
	 * @return array       
	 */
	public function parse($path) {
		$data = @parse_ini_file($path, true);

		if (!$data) {
			$error = error_get_last();
			if (!is_array($error)) {
                $error["message"] = "No parsable content in file.";
            }

            if (function_exists("error_clear_last")) {
                error_clear_last();
            }

            throw new ParseException($error);
		}

		return $this->expandKey($data);
	}

    /**
     * Expand array with dotted keys to multidimensional array
     *
     * @param array $data
     *
     * @return array
     */
	protected function expandKey($data) {
        foreach ($data as $key => $value) {
            if (($found = strpos($key, '.')) !== false) {
                $newKey = substr($key, 0, $found);
                $remainder = substr($key, $found + 1);

                $expandedValue = $this->expandDottedKey(array($remainder => $value));
                if (isset($data[$newKey])) {
                    $data[$newKey] = array_merge_recursive($data[$newKey], $expandedValue);
                } else {
                    $data[$newKey] = $expandedValue;
                }
                unset($data[$key]);
            }
        }
        return $data;
	}
}