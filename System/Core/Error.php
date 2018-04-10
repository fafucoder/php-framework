<?php

namespace System;

class Error {
    /**
     * 注册异常处理
     * @return void
     */
    public static function register() {
        set_error_handler(array(__CLASS__,'error_handler'));
        register_shutdown_function(array(__CLASS__,'shutdown_handler'));
        set_exception_handler(array(__CLASS__,'exception_handler'));

    }

    /**
     * 错误处理函数
     * @param  int $errno 错误行号
     * @param  string $error 错误信息
     * @param  string $file  错误文件
     * @param  int $line  错误行号
     * @return void
     */
    public static function error_handler($errno, $error, $file, $line) {
        var_dump($errno, $error, $file, $line);
        if (!self::isFatal($errno)) {
            ob_end_clean();
            $errorStr = "[$errno] $error " .$file. " 第 $line 行.";
            Log::save($errorStr);
            self::halt($errorStr);
        }
    }

    /**
     * 异常终止函数
     * @return void [description]
     */
    public static function shutdown_handler() {
        if (!is_null($e = error_get_last()) && self::isFatal($e['type'])) {
            $message = array(
                'errorfile:' => isset($e['file']) ?: "",
                'errorline:' => isset($e['line']) ?: null,
                'errortype'  => isset($e['type']) ?: "",
                'errormessage' => isset($e['message']) ?: ""
            );
            Log::write(json_encode($message));
            self::halt($e);
            exit;
        }
    }

    /**
     * 异常函数
     * @return void
     */
    public static function exception_handler($e) {
       $error = array();
       $error['message'] = $e->getMessage();
       $error['code'] = $e->getCode();
       $error['file'] = $e->getFile();
       $error['line'] = $e->getLine();
       $trace         =   $e->getTrace();
       $error['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : "" ;
       $error['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : "" ;
       $error['trace']    =   $e->getTraceAsString();
       header('HTTP/1.1 404 Not Found');
       header('Status:404 Not Found');
       self::halt($error);

    }

    /**
     * 显示exception
     * @param  exception  $e
     * @return void
     */
    public static function show_exception($e = array()) {
        $exception_templete = CORE_PATH . "Templete/exception.php";
        if (file_exists($exception_templete)) {
            include $exception_templete;
        }
        exit;
    }

    /**
     * 显示错误
     * @return void
     * @todo
     */
    public static function show_error() {
        echo "页面升级中～！";
        exit;
    }

    /**
     * 显示404
     * @return void
     */
    public static function show_404() {
        $heading = '404 Page Not Found';
        $message = 'The page you requested was not found.';
        $error_templete = CORE_PATH . 'Templete/error_404.php';
        include $error_templete;
        exit;
    }

    public static function halt($error) {
        $debug = defined("DEBUG") ? DEBUG : Config::get('debug');
        if ($debug) {
            if (!is_array($error)) {
                $trace = debug_backtrace();
                $e['message'] = $error;
                $e['file']    = $trace[0]['file'];
                $e['line']    = $trace[0]['line'];
                ob_start();
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
            } else {
                $e = $error;
            }
            self::show_exception($e);
        } else {
            self::show_error();
        }
    }

    /**
     * 确定错误类型是否致命
     * @access protected
     * @param  int $type 错误类型
     * @return bool
     */
    protected static function isFatal($type) {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

}
