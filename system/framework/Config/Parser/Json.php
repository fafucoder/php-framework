<?php
namespace Framework\Config\Parser;

use Framework\Config\Exception\ParseException;

class Json implements ParserInterface {
	/**
	 * Parses a file from path and get its contents as an array.
	 * 
	 * @param  string $path 
	 * @return array       
	 */	
	public function parse($path) {
        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message  = 'Syntax error';
            if (function_exists('json_last_error_msg')) {
                $error_message = json_last_error_msg();
            }

            $error = array(
                'message' => $error_message,
                'type'    => json_last_error(),
                'file'    => $path,
            );
            throw new ParseException($error);
        }

        return $data;
	}
}