<?php
namespace Framework\Environment;

use Framework\Config\Config;
use Framework\Environment\Exception\PathNotFoundException;
/**
 * Environment
 *
 * The class is inspired by Dotenv class https://github.com/vlucas/phpdotenv/blob/master/LICENSE.txt.
 *
 * @since 1.0.0
 */
class Environment {
	/**
	 * Set environment path.
	 * 
	 * @var string
	 */
	public $path;

	/**
	 * Set environemnt file parser type.
	 * 
	 * @var $string
	 */
	public $parser = 'yaml';

	/**
	 * Current environment type have production and development and test type.
	 * 
	 * @var string
	 */
	public $env;

	/**
	 * Current environment data.
	 * 
	 * @var array
	 */
	public $data;

	/**
	 * Construct.
	 * 
	 * @param string $path        environment directory
	 * @param string $environment environment mode
	 * @param string $parser      environment file parser
	 */
	public function __construct($path, $environment = 'development', $parser = 'yml') {
		$this->path = $this->getFilePath($path, $environment, $parser);
		$this->environemnt = $environment;
		$this->parser = $parser;
	}

	public function setEnv() {

	}

	public function getEnv() {

	}

	/**
	 * Load environment data.
	 * 
	 * @return array 
	 */
	public function load() {
		return $this->getDate();
	}

	/**
	 * Reload environment config data.
	 * 
	 * @return array
	 */
	public function reload() {
		return $this->getData(false);
	}

	public function getData() {

	}
	
	/**
	 * Get environment file path.
	 * 
	 * @param  string $path       file path
	 * @param  string $environment env mode
	 * @param  string $parser      parser
	 * @return string|Exception
	 */
	public function getFilePath($path, $environment = 'development', $parser = 'yml') {
		if (file_exists($path)) {
			if (!is_dir($path)) {
				return $path;
				
			}
			$filepath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $environment . '.' . $parser;

			if (file_exists($filepath)) {
				return $filepath;
			}

			$paths = glob($path . "/{$environment}.*", GLOB_NOSORT);
			if (empty($paths)) {
				throw new PathNotFoundException(sprintf("environment %s file not found!", $environment));
			}

			return $paths[0];
		}

		throw new PathNotFoundException(sprinf('path %s not exists!', $path));
	}
}