<?php 
namespace System\Drivers\Log;
use System\Config;

class File {
    
    public function write($message,$dest='') {
        if(empty($dest)){
            $dest = Config::get('log.path').date('Ymd').'.log';
        }
        // 自动创建日志目录
        $log_dir = dirname($dest);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }        
        file_put_contents($dest, $message,FILE_APPEND | LOCK_EX);
    }
}