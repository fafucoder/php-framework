<?php

define('EXT', '.php');

define('SP', DIRECTORY_SEPARATOR);

defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__DIR__)) . SP);

defined('SYSTEM_PATH') or define('SYSTEM_PATH', dirname(__DIR__). SP );

define('CORE_PATH', SYSTEM_PATH . 'Core' . SP);

defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'Application' . SP);

defined('VIEW_PATH') or define('VIEW_PATH', APP_PATH . SP);

defined('ENVIRONMENT') or define('ENVIRONMENT','development');

defined("DEBUG") or define("DEBUG",true);
