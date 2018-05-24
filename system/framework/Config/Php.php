<?php
namespace System\Drivers\Conf;

class Php implements Adapter {
	public function parse($config) {
		if (is_file($config)) {
			return include($config);
		}
	}
}