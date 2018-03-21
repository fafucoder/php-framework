<?php

namespace System;

class Config {

    /**
     * List all config
     */
    protected static $config = array();

    /**
     * List of all loaded config files
     */
    public static $is_load = array();

    /**
     * container of config path
     */
    public static $config_path = array();

    /**
     * 解析配置文件或内容
     * @param  string $config 配置文件路径或内容
     * @param  string $type   配置解析类型
     * @param  string $name   配置名（如设置即表示二级配置）
     * @return mixed
     */
    public static function parse($file, $type = '', $name = '') {
        if (empty($type)) {
            $type = pathinfo($file, PATHINFO_EXTENSION);
        }
        if ($type == 'php') {
            return self::set(include $file, $name);
        }
        $class = false !== strpos($type, '\\') ?
            $type :
            '\\System\\Drivers\\ParseConf\\' . ucwords($type);
        var_dump($class);
        return self::set((new $class())->parse($config), $name);
    }

    /**
     * 加载配置文件  
     * @param  string $file 文件名
     * @param  string $name 配置名
     * @return mixed
     */                 
    public static function load($file='', $name = "") {
        if (!is_file($file)) {
            $file = $file . CONF_EXT;
        }
        $filename= pathinfo($file, PATHINFO_FILENAME);
        if (array_key_exists($filename, self::$is_load)) {
            return self::$is_load[$filename];
        }
        if ($file = self::findFile($file)) {
            $config = self::parse($file, $name);
            self::$is_load[$filename] = $config;
            return $config;
        }
        throw new \Exception("file not found");
    }

    public static function has($name) {
        if (!strpos($name, '.')) {
            return isset(self::$config[strtolower($name)]);
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);
        return isset(self::$config[strtolower($name[0])][$name[1]]);
    }

    /**
     * 设置配置参数 name 为数组则为批量设置
     * @access public
     * @param  string|array $name  配置参数名（支持二级配置 . 号分割）
     * @param  mixed        $value 配置值
     * @return mixed
     */
    public static function set($name, $value = null ) {
        // 字符串则表示单个配置设置
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                self::$config[strtolower($name)] = $value;
            } else {
                // 二维数组
                $name = explode('.', $name, 2);
                self::$confi[strtolower($name[0])][$name[1]] = $value;
            }
            return $value;
        }

        // 数组则表示批量设置
        if (is_array($name)) {
            if (!empty($value)) {
                self::$config[$value] = isset(self::$config[$value]) ?
                    array_merge(self::$config[$value], $name) :
                    $name;
                return self::$config[$value];
            }

            return self::$config = array_merge(
                self::$config, array_change_key_case($name)
            );
        }

        return self::$config;

    }

    /**
     * 获取特定的值
     * @param  string $name key
     * @return string|null   
     */
    public static function get($name = "") {

        if (empty($name)) {
            return self::$config;
        }
        // 非二级配置时直接返回
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            return isset(self::$config[$name]) ? self::$config[$name] : null;
        }

        // 二维数组设置和获取支持
        $name    = explode('.', $name, 2);
        $name[0] = strtolower($name[0]);
        return isset(self::$config[$name[0]][$name[1]]) ?
            self::$config[$name[0]][$name[1]] :
            null;
    }

    /**
     * 清空配置
     * @return null 
     */
    public function clear() {
        self::$config = array();
        self::$is_load= array();
    }


    /**
     * 删除某个配置
     */
    public function remove($name) {
        if (array_key_exists($name, self::$config)) {
            unset(self::$config[$name]);
        }
    }

    /**
     * 设置config路径
     * @param array  $path  路径
     * @param boolean $merge 是否merge
     */
    public static function setConfigPath($path = array(), $merge = false) {
        if ($merge) {
            self::$config_path　= array_merge(self::$config_path, $path);
        } else {
            self::$config_path = $path;
        }
    }

    /**
     * get config path
     * @return array|null
     */
    public static function getConfigPath() {
        return self::$config_path;
    }


    /**
     * @param string $filename 文件名
     */
    public static function findFile($filename) {
        foreach (self::$config_path as $path) {
            $file = $path . $filename;
            if (file_exists($file)) {
                return $file;
            }
        }
    }
}

