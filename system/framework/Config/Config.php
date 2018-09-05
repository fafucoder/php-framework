<?php
namespace Framework\Config;

use Framework\Config\Exception\FileNotFoundException;
use Framework\Config\Exception\EmptyDirectoryException;
use Framework\Config\Exception\ParserInvalidException;

class Config extends AbstractConfig  {
	/**
	 * Config file parser adapters.
	 * 
	 * @var array
	 */
	public $parsers = array(
		'xml' => 'Framework\Config\Parse\Xml',
		'ini' => 'Framework\Config\Parse\Ini',
		'json' => 'Framework\Config\Parse\Json',
		'php' => 'Framework\Config\Parse\Php',
		'yml' => 'Framework\Config\Parse\Yaml',
	);

	/**
	 * Check if cache confi data.
	 * 
	 * @var boolean
	 */
	public $cache = true;

	/**
	 * Config path.
	 * 
	 * @var array
	 */
	public $path = array();

	/**
	 * Construct.
	 * 
	 * @param string|array $path   config path
	 * @param string $parser parser type
	 */
	public function __construct($path, config = array()) {
		if (isset($config['cache'])) {
			$this->cache = $config['cache'];
		}

		$extension = '';
		$data = array();
		
		if (isset($config['parser'])) {
			$extension = $config['parser'];
		}

		$this->path = $this->getPath($path);
		foreach ($this->path as $path) {
			$data = array_merge($data, (array) self::parse($path, $extension));
		}

		parent::__construct($data);
	}

	/**
	 * Set config path.
	 * 
	 * @param string|array $path 
	 */
	public function setPath($path) {
		$this->path = $this->loadPath($path);
	}

	/**
	 * Get config path.
	 * 
	 * @return array 
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Parse config file.
	 * 	
	 * @param  string $file  config file
	 * @param  string $parse parse type
	 * 
	 * @return array
	 */
	public static function parse($file, $parse = '') {
		if (empty($parse)) {
			$parse = pathinfo($file, PATHINFO_EXTENSION);
		}
		$parser = $this->getParser($parse);
		
		return $parser->parse($path);
	}

	/**
	 * Get a parser for a given file extension.
	 * 
	 * @param  string $extension
	 * @return object        
	 */
	private function getParser($extension) {
		if (array_key_exists($extension, $this->parsers)) {
			return new $this->parsers[$extension];
		}

		throw new ParserInvalidException('Unsupport configuration format!');
	}

	/**
	 * Get config path files.
	 * 
	 * @param  string $path 
	 * @return array      
	 */
	public function loadPath($path) {
		if (is_array($path)) {
			return $this->getFiles($path);
		}

		if (is_dir($path)) {
			$paths = glob($path . "/*.*");
			if (empty($paths)) {
				throw new EmptyDirectoryException("Config directory: $path is empty!");
			}

			return $paths;
		}

		if (!file_exists($path)) {
			throw new FileNotFoundException（"Config file: $path not found!");
		}

		return array($path);
	}

	/**
	 * Get config path files.
	 * 
	 * @param  array $path 
	 * @return array       
	 */
	private function getFiles($path) {
		$paths = array();

		foreach ($path as $p) {
			try {
				$paths = array_merge($paths, $this->loadPath($p));
			} catch (FileNotFoundException $e) {
				throw $e;
			}
		}

		return $paths;
	}
}