<?php
namespace Framework\Redis;

use Framework\Redis\Exceptions\DriverNotFoundException;

class Redis {
	/**
	 * Redis config.
	 * 
	 * @var array
	 */
	public $config = array();

	/**
	 * Singleton instance.
	 * 
	 * @var object
	 */
	private static $instance;

	/**
	 * The redis connect driver name.
	 * 
	 * @var string
	 */
	public $driver;

	/**
	 * Connector type.
	 * 
	 * @var array
	 */
	public $connetors = array(
		'redis' => 'Framework\\Redis\\Connectors\\Redis',
		'predis' => 'Framework\\Redis\\Connectors\\Predis',
	);

	/**
	 * Connected redis instance.
	 * 
	 * @var object 
	 */
	public $connect;

	/**
	 * Construct.
	 * 
	 * @param array $config 
	 */
	public function __construct(array $config = array()) {
		$config = array_merge($this->defaultConfig(), $config);
		if (isset($config['driver'])) {
			$this->driver = $config['driver'];
			unset($config['driver']);
		} else {
			$this->driver = 'redis';
		}
		$this->config = $config;
	}

	/**
	 * Redis default config.
	 * 
	 * @return array 
	 */
	public function defaultConfig() {
		return array(
			'host' => '127.0.0.1',
			'port' => 6379,
			'password' => '',
			'timeout' => 0,
			'option' => array(),
		);
	}

	/**
	 * Connect to redis.
	 * 
	 * @return object 
	 */
	public function connect() {
		if (is_array($this->driver, $this->connectors)) {
			$connect = new $this->connectors[$this->driver];
			$this->connect = $connect;
			return $connect;
		}

		throw new DriverNotFoundException('driver not found for %s', $this->driver);
	}

	/**
	 * Disconnect to redis.
	 * 
	 * @return void 
	 */
	public function disconnect() {
		if ($this->connect) {
			$this->connect->disconnect();
		}
	}

	/**
	 * Single instance.
	 * 
	 * @param  array  $config 
	 * @return object         
	 */
	public function instance(array $config = array()) {
		if (!self::$instance || !self::$instance instanceof self) {
			self::$instance = new self($config);
		}
		return self::$instance;
	}
}