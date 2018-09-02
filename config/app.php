<?php

return [

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
    // 控制器类后缀
    'controller_suffix'      => 'Controller',
    // 默认输出类型
    'default_return_type'    => 'html',
    // pathinfo分隔符
    'pathinfo_depr'          => '/',  //index.php/Index/index/......
    //rewrite模式默认模块符
    'var_pathinfo'           => 'r',  //index.php?r=Index/index&id=2.....
    //pathinfo fetch
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    //var_method
    'var_method'             => '_method',
    //额外的文件
    'extra_file_list'        => array(),
    //是否开启debug
    'debug'                  => true,
    // URL伪静态后缀
    'url_html_suffix'        => 'html',

    // +-----------------------------------------------------------------
    // | 模板设置
    // +-----------------------------------------------------------------

    'template'    => [
        'view_suffix'            => 'html',

        'layout_on'              => true,

        'layout_path'            => 'layout',

        'layout_content_replace' => '{__CONTENT__}',

        'dispatch_success_tmpl'  => '',

        'dispatch_error_tmpl'    => '',

        'exception_tmpl'         =>'',

        'error_message'          => '页面错误！请稍后再试～',

        'success_message'        => '操作成功！',

        'cache'                  => false,

        'debug'                  => false,
    ],

    // +-----------------------------------------------------------------
    // | 会话设置
    // +-----------------------------------------------------------------
    'session'    => [
        'id'             => '',

        'session_id'     => '',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',

        'auto_start'     => true,
    ],

    // +---------------------------------------------------------------
    // | Cookie设置
    // +---------------------------------------------------------------
    'cookie'   => [
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,

        'setcookie' => true,
    ],

    // +-----------------------------------------------------------------
    // | 数据库设置
    // +-----------------------------------------------------------------
    'database'            => [
        // 数据库类型
        'database_type'   => 'mysql',
        // 数据库连接DSN配置
        'database_dsn'    => [],
        // 服务器地址
        'server'          => '127.0.0.1',
        // 数据库名
        'database_name'   => '',
        // 数据库用户名
        'username'        => '',
        // 数据库密码
        'password'        => '',
        // 数据库连接端口
        'port'            => '',
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库调试模式
        'debug'           => false,
        //log
        'logging'         => true,
        //socker
        'socket'          => '',
        //options
        'option'          => [],
        //command
        'cammand'         => [],
    ],

    // +-----------------------------------------------------------------
    // | 日志设置
    // +-----------------------------------------------------------------
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

    // +-----------------------------------------------------------------
    // | 缓存设置
    // +-----------------------------------------------------------------
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
];
