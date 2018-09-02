<?php
namespace Framework\Config\Parser;

use Exception;
use Framework\Config\Exception\ParseException;

class Php implements ParserInterface {
	/**
	 * Parses a file from path and get its contents as an array.
	 * 
	 * @param  string $path 
	 * @return array       
	 */	
	public function parse($path) {
        try {
            $data = require $path;
        } catch (Exception $exception) {
            throw new ParseException(
                array(
                    'message'   => 'PHP file threw an exception',
                    'exception' => $exception,
                )
            );
        }
        if (is_callable($data)) {
            $data = call_user_func($data);
        }

        if (!is_array($data)) {
            throw new \UnexpectedValueException('PHP file does not return an array');
        }

        return $data;
	}
}