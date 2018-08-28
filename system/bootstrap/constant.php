<?php

define('EXT', '.php');

defined('CONF_EXT') or define('CONF_EXT','.php');

defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__DIR__)) . SP);

defined('SYSTEM_PATH') or define('SYSTEM_PATH', dirname(__DIR__). SP );

defined('CORE_PATH') or define('CORE_PATH', SYSTEM_PATH . 'Core' . SP);

defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'Application' . SP);

defined('VIEW_PATH') or define('VIEW_PATH', APP_PATH . 'Views' . SP);

defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH . 'Models' . SP);

defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH . 'Controllers' . SP);

defined('CONF_PATH') or define('CONF_PATH', APP_PATH . 'Conf' . SP);

defined('COMMON_PATH') or define('COMMON_PATH', APP_PATH . 'Common' . SP);

defined('LOG_PATH') or define("LOG_PATH", APP_PATH . 'Runtime/Log' . SP );

defined("CACHE_PATH") or define("CACHE_PATH", APP_PATH . 'Runtime/Cache' . SP);

defined('ENVIRONMENT_PATH') or define('ENVIRONMENT_PATH', ROOT_PATH . 'Environments' . SP);

defined("PUBLIC_PATH") or define("PUBLIC_PATH", ROOT_PATH . 'Public' . SP);

defined('ENVIRONMENT') or define('ENVIRONMENT','development');

defined("DEBUG") or define("DEBUG",true);
