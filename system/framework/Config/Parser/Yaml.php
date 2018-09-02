<?php
namespace Framework\Config\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml as YamlParser;
use Framework\Config\Exception\ParseException;

class Yaml implements ParserInterface {
	/**
	 * Parses a file from path and get its contents as an array.
	 * 
	 * @param  string $path 
	 * @return array       
	 */	
	public function parse($path) {
		try {
			$data = YamlParser::parse(file_get_contents($path), YamlParser::PARSE_CONSTANT);
		} catch (Exception $e) {
            throw new ParseException(
                array(
                    'message'   => 'Error parsing YAML file',
                    'exception' => $e,
                )
            );
		}
	}
}