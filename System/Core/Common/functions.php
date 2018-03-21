<?php

function p($var) {
	var_dump($var);
}

if (!function_exists('config')) {
	function config($name ="") {
		return \System\Config::get($name);
	}
}

