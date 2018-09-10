<?php
namespace Framework\Redis\Connectors;

use Framework\Redis\Exceptions\ExtendNotLoadException;

class Redis {
	/**
	 * Redis instance.
	 * 
	 * @var object 
	 */
	public $redis;

	/**
	 * Redis connect options.
	 * 
	 * @var array
	 */
	public $options = array();

	/**
	 * connect Redis.
	 * 
	 * @param  array  $config 
	 * @return object      
	 */
	public function connect(array $config = array()) {
		if (isset($config['options']) {
			$this->options = $config['options'];
			unset($config['options']);
		}

		if (extension_loaded('redis')) {
			$this->redis = new \Redis();

			try {
				if ($this->options['persistent']) {
					$this->redis->pconnect($config['host'], $config['post'], $config['timeout'], 'persistent_id_' . $this->options['persistent_id']);
				} else {
					$this->redis->connect($config['host'], $config['port'], $config['timeout']);
				}	
			} catch (Exception $e) {
				throw new InvalidArgumentException(sprintf('Redis connection failed (%s)', $e->getMessage()));
			}

			if ('' ！= $config['password']) {
				$this->redis->auth($config['password']);
			}

			if (0 != $this->options['select']) {
				$this->redis->select($this->options['select']);
			}

			return $this->redis;
		}

		throw new ExtendNotLoadException('redis extension not load!');
	}

	/**
	 * Redis disconnect.
	 *
	 * info: if is pconnect method disconnect function not take work.
	 * 
	 * @return void 
	 */
	public function disconnect() {
		$this->redis->close();
	}
}