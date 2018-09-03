<?php
namespace Framework\Environment;

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
	 * Construct.
	 * 
	 * @param array $config 
	 */
	public function __construct( array $config = array()) {

	}
}