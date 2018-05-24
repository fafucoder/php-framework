<?php
namespace System\Drivers\Conf;

interface Adapter {
	public function parse($config);
}