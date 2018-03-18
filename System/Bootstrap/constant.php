<?php

define('EXT', '.php');

define('SP', DIRECTORY_SEPARATOR);

defined('SYSTEM_PATH') or define('SYSTEM_PATH', dirname(__DIR__). SP );

define('CORE_PATH', SYSTEM_PATH . 'Core' . SP);

defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . SP);

defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__DIR__)) . SP);

defined("DEBUG") or define("DEBUG",true);
