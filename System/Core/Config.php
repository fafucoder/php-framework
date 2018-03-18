<?php

namespace System;

class Config {

    /**
     * List all config
     */
    protected $conf = array();

    /**
     * List of all loaded config files
     */
    public $is_load = array();

    /**
     * 解析配置文件或内容
     * @access public
     * @param  string $config 配置文件路径或内容
     * @param  string $type   配置解析类型
     * @param  string $name   配置名（如设置即表示二级配置）
     * @return mixed
     */
    public static function parse($config, $type = '', $name = '')
    {
        if (empty($type)) $type = pathinfo($config, PATHINFO_EXTENSION);

        $class = false !== strpos($type, '\\') ?
            $type :
            '\\think\\config\\driver\\' . ucwords($type);

        return self::set((new $class())->parse($config), $name, $range);
    }

    public static function load($file, $name = "") {

    }

    public static function has($name) {

    }

    public static function set($name, $value = null ) {

    }

    public function reset() {

    }

}
