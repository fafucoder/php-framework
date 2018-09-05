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
	public $environment;

	/**
	 * Current environment data.
	 * 
	 * @var array
	 */
	public $data = array();

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

	/**
	 * Set environment.
	 * 
	 * @param string $environment 
	 */
	public function setEnvironment($environment) {
		$this->environment = $environment;
	}

	/**
	 * Get environment.
	 * 
	 * @return string
	 */
	public function getEnvironment() {
		return $this->environment;
	}

	/**
	 * Load environment data.
	 * 
	 * @return array 
	 */
	public function load() {
		if ($this->path) {
			$this->data = Config::parse($this->path);

			//set env
			foreach ($this->data as $key => $value) {
				if (is_string($value)) {
					putenv("$key=$value");
				}

				//if is array set $key_$k type
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						putenv("$key_$k=$v");
					}
				}
			}
		}
	}

	/**
	 * Get environment variable.
	 * 
	 * @param  string $name    variable name
	 * @param  string $default default value
	 * @return mixed
	 */
	public function getVar($name, $default = null) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		$name = str_replace(".", "_", $name);
		if (array_key_exists($name, $_ENV)) {
			return $_ENV[$name];
		} elseif (array_key_exists($name, $_SERVER)) {
			return $_SERVER[$name];
		}

		$value = getenv($name);
		if ($value) {
			return $value;
		}

		return $default;
	}
	
	/**
	 * Set environment variable.
	 * 
	 * @param string $name variable name 
	 * @param mixed $value variable value
	 */
	public function setVar($name, $value = null) {
		if (!is_array($name)) {
			$this->setEnvironmentVariable($name, $value);
		}

		foreach ($name as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					$name = $key . '_' . $k;
					$this->setEnvironmentVariable($name, $v);
				}
			} else {
				$this->setEnvironmentVariable($key, $val);
			}
		}
	}

	/**
	 * Remove environment variable.
	 * 
	 * @param  string $name variable name
	 * @return void       
	 */
	public function removeVar($name) {
		if (array_key_exists($name, $this->data)) {
			unset($this->data[$name]);
		}

		$name = str_replace(".", "_", $name);
		if (function_exists('putenv')) {
			putenv($name);
		}
		unset($_ENV[$name], $_SERVER[$name]);
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

	/**
	 * Set environment variable.
	 * 
	 * @param string $name
	 * @param mixed $value 
	 */
	protected function setEnvironmentVariable($name, $value) {
        if (function_exists('apache_getenv') && function_exists('apache_setenv')) {
            apache_setenv($name, $value);
        }

        if (function_exists('putenv')) {
            putenv("$name=$value");
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
	}
}