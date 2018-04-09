一．环境要求：
1.php版本　php>=5.3 
2.mysql版本　：未知
3.操作系统：window＋linxu

二.入口文件：

三.配置文件：Config.php实现的功能
1.支持二级配置

$config = [
    'cache'　=> [
        'type'   => 'File',
        'path'   => CACHE_PATH,
        'prefix' => '',
        'expire' => 0,
    ],
];

Config::set($config);

２．支持二级获取
Config::get('user.type');

３．支持三种配置格式
xml　ini　json

4.拥有助手函数　config()


四.路由文件：Route.php
1.支持两种路由格式：
pathinfo	index.php/index/index/
queryString  index.php?c='index'&f='index'

2.可以自定义路由
＇login'=>'user/login'

五.控制器：Controller.php
1.支持前后置操作
beforeAction()
AfterAction()

2.具有render方法

3.路由布局方法

．．．．．．





