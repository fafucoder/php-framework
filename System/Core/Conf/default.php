<?php

return [

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
        //日志文件后缀
        'ext' => EXT,
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    
    'app' => [
        // 默认控制器名
        'default_controller'     => 'Index',
        // 默认操作名
        'default_action'         => 'index',
        // 默认的空控制器名
        'empty_controller'       => 'Empty',
        // 操作方法前缀
        'action_prefix'      => false,
        // 操作方法后缀
        'action_suffix'          => '',
        // 应用类库后缀
        'class_suffix'           => false,
        // 控制器类后缀
        'controller_suffix'      => false,
        // 默认输出类型
        'default_return_type'    => 'html',
        //路由模式
        'url_mode'               => 1,
        // pathinfo分隔符
        'pathinfo_depr'          => '/',  //index.php/Index/index/......
        //rewrite模式默认模块符
        'rewrite_depr'           => 'r',  //index.php?r=Index/index&id=2.....
        // 默认AJAX 数据返回格式,可选json xml ...
        'default_ajax_return'    => 'json',
        // 默认JSONP格式返回的处理方法
        'default_jsonp_handler'  => 'jsonpReturn',
        // 默认语言
        'default_lang'           => 'zh-cn',
        // 默认时区
        'default_timezone'       => 'PRC',
        // 是否开启多语言
        'lang_switch_on'         => false,
        // 默认全局过滤方法 用逗号分隔多个
        'default_filter'         => '',
        // URL参数方式 0 按名称成对解析 1 按顺序解析
        'url_param_type'         => 0,
        // 是否开启路由
        'url_route_on'           => true,
        'var_method'             => '_method',
    ],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'    => [
        // 模板后缀
        'view_suffix'  => 'html',
        // 默认跳转页面对应的模板文件
        'dispatch_success_tmpl'  => '',
        'dispatch_error_tmpl'    => '',
        // 异常页面的模板文件
        'exception_tmpl'         =>'',
        // 错误显示信息,非调试模式有效
        'error_message'          => '页面错误！请稍后再试～',
        'success_message'        => '操作成功！',
    ],


    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'    => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
        'httponly'       => true,
        'secure'         => false,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'   => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // +----------------------------------------------------------------------
    // | 数据库设置
    // +----------------------------------------------------------------------

    'database'               => [
        // 数据库类型
        'type'            => 'mysql',
        // 数据库连接DSN配置
        'dsn'             => '',
        // 服务器地址
        'hostname'        => '127.0.0.1',
        // 数据库名
        'database'        => '',
        // 数据库用户名
        'username'        => 'root',
        // 数据库密码
        'password'        => '',
        // 数据库连接端口
        'hostport'        => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器数量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 数据集返回类型
        'resultset_type'  => 'array',
        // 自动写入时间戳字段
        'auto_timestamp'  => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
    ],
];
