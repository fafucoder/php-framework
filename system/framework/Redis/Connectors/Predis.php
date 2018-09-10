<?php
namespace Framework\Redis\Connectors;

use Predis\Client;

class Predis {
	/**
	 * Predis instance.
	 * 
	 * @var object
	 */
	public $predis;

	/**
	 * Predis connect options.
	 * 
	 * @var array
	 */
	public $options = array();

	/**
	 * Predis to connect redis.
	 * 
	 * @param  array  $config 
	 * @return object         
	 */
	public function connect(array $config = array()) {
		if (isset($config['options'])) {
			$this->options = $config['options'];
			unset($config['options']);
		}

		if (class_exists(Client::class)) {
			$this->predis = new Client($config, $this->options);
			return $this->predis;
		}

		throw new \InvalidArgumentException(sprintf("Predis class not found!"));
	}

	/**
	 * Predis disconnect to redis.
	 * 
	 * @return void 
	 */
	public function disconnect() {
		$this->predis->quit();
	}
}