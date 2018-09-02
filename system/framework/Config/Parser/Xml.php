<?php
namespace Framework\Config\Parser;

use Framework\Config\Exception\ParseException;

class Xml implements ParserInterface {
	/**
	 * Parses a file from path and get its contents as an array.
	 * 
	 * @param  string $path 
	 * @return array       
	 */	
	public function parse($path) {
        libxml_use_internal_errors(true);

        $data = simplexml_load_file($path, null, LIBXML_NOERROR);

        if ($data === false) {
            $errors      = libxml_get_errors();
            $latestError = array_pop($errors);
            $error       = array(
                'message' => $latestError->message,
                'type'    => $latestError->level,
                'code'    => $latestError->code,
                'file'    => $latestError->file,
                'line'    => $latestError->line,
            );
            throw new ParseException($error);
        }

        $data = json_decode(json_encode($data), true);

        return $data;
	}
}