<?php 
namespace System;

class Request {
    /**
     * @var object 对象实例
     */
    protected static $instance;

    /**
     * 请求方法 [get|post|...]
     * @var string
     */
    protected $method;

    /**
     * 请求域名 【http://www.baidu.com】
     * @var [type]
     */
    protected $domain;

    /**
     * 请求的url [http://www.baidu.com/index/index?id=2]
     * @var string
     */
    protected $url;

    /**
     * 请求基地址 【/index/index?id=2】
     * @var string
     */
    protected $baseUrl;

    /**
     * 当前入口文件 [index.php]
     * @var string
     */
    protected $baseFile;

    /**
     * 根地址 [localhost/miniphp]
     * @var string
     */
    protected $root;

    /**
     * path_info信息 [/index/index.html]
     * @var string
     */
    protected $pathinfo;

    /**
     * 当前路径 [/index/index]
     * @var string
     */
    protected $path;

    /**
     * 路由信息
     * @var array
     */
    protected $routeInfo = array();

    /**
     * 当前控制器
     * @var string
     */
    protected $controller;

    /**
     * 当前action
     * @var string
     */
    protected $action;

    /**
     * 当前语言
     * @var string
     */
    protected $langset;

    /**
     * 请求参数
     * @var array
     */
    protected $param   = [];
    protected $get     = [];
    protected $post    = [];
    protected $request = [];
    protected $route   = [];
    protected $put     = [];
    protected $session = [];
    protected $file    = [];
    protected $cookie  = [];
    protected $server  = [];
    protected $header  = [];

    /**
     * @var array 资源类型
     */
    protected $mimeType = [
        'xml'   => 'application/xml,text/xml,application/x-xml',
        'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'    => 'text/javascript,application/javascript,application/x-javascript',
        'css'   => 'text/css',
        'rss'   => 'application/rss+xml',
        'yaml'  => 'application/x-yaml,text/yaml',
        'atom'  => 'application/atom+xml',
        'pdf'   => 'application/pdf',
        'text'  => 'text/plain',
        'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
        'csv'   => 'text/csv',
        'html'  => 'text/html,application/xhtml+xml,*/*',
    ];
    
    protected $content;

    /**
     * 过滤规则
     * @var string
     */
    protected $filter;
    
    /**
     * hook
     * @var array
     */
    protected static $hook = [];
    
    /**
     * php input
     * @var input
     */
    protected $input;

    /**
     * 构造函数
     * @param array $options 
     */
    protected function __construct($options = array()) {
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
        if (is_null($this->filter)) {
            $this->filter = "htmlentities";
        }
        $this->input = file_get_contents('php://input');
    }

    /**
     * [__call description]
     * @param  mixed $method 方法名
     * @param  array $args   参数
     * @return mixed         
     */
    public function __call($method, $args) {
        if (array_key_exists($method, self::$hook)) {
            array_unshift($args, $this);
            return call_user_func_array(self::$hook[$method], $args);
        } else {
            throw new \Exception("method not exist:" . __CLASS__ . '->' . $method);
        }
    }

    /**
     * 钩子函数
     * @param  string|array $method   方法名
     * @param  mixed $callback callback
     * @return void           
     */
    public static function hook($method,$callback = null) {
        if (is_array($method)) {
            self::$hook = array_merge(self::$hook,$method);
        } else {
            self::$hook[$method] = $callback;
        }
    }

    /**
     * 实例化
     * @param  array  $option 参数
     * @return object         
     */
    public static function instance($options = array()) {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 创建请求
     * @param  string $uri     url
     * @param  string $method  请求方式
     * @param  array  $params  参数
     * @param  array  $cookie  cookie
     * @param  array  $files   files
     * @param  array  $server  
     * @param  mixed $content 
     * @return object          
     */     
    public static function create($uri, $method = 'GET', $params = [], $cookie = [], $files = [], $server = [], $content = null) {
        $server['PATH_INFO'] = "";
        $server['REQUEST_METHOD'] = strtoupper($method);
        $info = parse_url($uri);
        if (isset($info['host'])) {
            $server['SERVER_NAME'] = $info['host'];
            $server['HTTP_HOST'] = $info['host'];
        }

        if (isset($info['scheme'])) {
            if ('https' === $info['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['hTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }

        if (isset($info['port'])) {
            $server['SERVER_PORT'] = $info['port'];
            $server['HTTP_HOST'] = $server['HTTP_HOST'] . ":" . $info['port'];
        }

        if (isset($info['user'])) {
            $server['PHP_AUTH_USER'] = $info['user'];
        }

        if (isset($info['pass'])) {
            $server['PHP_AUTO_PW'] = $info['pass'];
        }

        if (!isset($info['path'])) {
            $info['path'] = '/';
        }

        $options = array();

        $options[strtolower($method)] = $params;
        $querystring = "";

        if (isset($info['query'])) {
            parse_str(html_entity_decode($info['query']),$query);
            if (!empty($params)) {
                $params = array_replace($query, $params);
                $querystring = http_build_query($query,"","&");
            } else {
                $params = $query;
                $querystring = $info['query'];
            }
        } elseif (!empty($params)) {
            $querystring = http_build_query($params,"","&");
        }

        if ($querystring) {
            parse_str($querystring,$get);
            $options['get'] = isset($options['get']) ? array_merge($get,$options['get']) : $get;
        }

        $server['REQUEST_URI'] = $info['path'] . ('' !== $querystring ? "?" . $querystring : "");
        $server['QUERY_STRING'] = $querystring;
        $options['cookie'] = $cookie;
        $options['param'] = $params;
        $options['file'] = $files;
        $options['server'] = $server;
        $options['url'] = $server['REQUEST_URI'];
        $options['pathinfo'] = '/' == $info['path'] ? "/" : ltrim($info['path'], '/');
        $options['method'] = $method;
        $options['domain'] = isset($info['scheme']) ? $info['scheme'] . "://" . $server['HTTP_HOST'] : "";
        $options['content'] = $content;
        self::$instance = new self($options);
        return self::$instance;
    }

    /**
     * 设置或者获取域名
     * @param  string|null $domain 域名
     * @return this        
     */
    public function domain($domain = null) {
        if (!is_null($domain)) {
            $this->domain = $domain;
            return $this;
        } elseif (!$this->domain) {
            $this->domain = $this->scheme() . "://" . $this->host();
        }
        return $this->domain;
    }

    /**
     * url
     * @param  null|string $url url地址
     * @return string|obj      
     */
    public function url($url = null) {
        if (!is_null($url) && true !== $url) {
            $this->url = $url;
            return $this;
        } elseif (!$this->url) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $this->url = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $this->url = $_SERVER['REQUEST_URI'];
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
                $this->url = $_SERVER['ORIG_PATH_INFO'] . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
            } else {
                $this->url = "";
            }
        }
        return true === $url ? $this->domain() . $this->url : $this->url;
    }

    /**
     * 获取或者设置baseurl
     * @param  string|null $url 
     * @return obj|string
     */
    public function baseUrl($url = null) {
        if (!is_null($url) && true !== $url) {
            $this->url = $url;
            return $this;
        } elseif (!$this->baseUrl) {
            $str = $this->url();
            $this->baseUrl = strpos($str, "?") ? strstr($str, "?", true) : $str;
        }
        return true === $url ? $this->domain() . $this->baseUrl : $this->baseUrl;
    }

    /**
     * 获取或者设置basefile
     * @param  string|null $file 
     * @return obj|string       
     */ 
    public function baseFile($file = null) {
        if (!is_null($file) && true !== $file) {
            $this->baseFile = $file;
            return $this;
        } elseif (!$this->baseFile) {
            $url = '';
            $script_name = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $script_name) {
                $url = $_SERVER['SCRIPT_NAME'];
            } elseif (basename($_SERVER['PHP_SELF']) === $script_name) {
                $url = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $script_name) {
                $url = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $script_name)) !== false) {
                $url = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $script_name;
            } elseif (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
                $url = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            }
            $this->baseFile = $url;
        }
        return true === $file ? $this->domain() . $this->baseFile : $this->baseFile;
    }

    /**
     * 获取根路径
     * @param  null|string $url 
     * @return string|obj      
     */
    public function root($url = null) {
        if (!is_null($url) && true !== $url) {
            $this->root = $url;
            return $this;
        } elseif (!$this->root) {
            $file = $this->baseFile();
            if ($file && 0 !== strpos($this->url(), $file)) {
                $file = str_replace('\\', '/', dirname($file));
            }
            $this->root = rtrim($file, '/');
        }
        return true === $url ? $this->domain() . $this->root : $this->root;
    }

    /**
     * pathinfo 信息(含后缀)
     * @return string 
     * @todo  
     */
    public function pathinfo() {
        if (is_null($this->pathinfo)) {
            if (isset($_GET[Config::get('var_pathinfo')])) {
                // 判断URL里面是否有兼容模式参数
                $_SERVER['PATH_INFO'] = $_GET[Config::get('var_pathinfo')];
                unset($_GET[Config::get('var_pathinfo')]);
            } 

            // 分析PATHINFO信息
            if (!isset($_SERVER['PATH_INFO'])) {
                foreach (Config::get('pathinfo_fetch') as $type) {
                    if (!empty($_SERVER[$type])) {
                        $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type], $_SERVER['SCRIPT_NAME'])) ?
                        substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME'])) : $_SERVER[$type];
                        break;
                    }
                }
            }
            $this->pathinfo = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
        }
        return $this->pathinfo;
    }

    /**
     * 获取path信息（不含后缀）
     * @return string 
     * @todo  pathinfo()
     */
    public function path() {
        if (is_null($this->path)) {
            $suffix   = Config::get('url_html_suffix');
            $pathinfo = $this->pathinfo();
            if (false === $suffix) {
                // 禁止伪静态访问
                $this->path = $pathinfo;
            } elseif ($suffix) {
                // 去除正常的URL后缀
                $this->path = preg_replace('/\.(' . ltrim($suffix, '.') . ')$/i', '', $pathinfo);
            } else {
                // 允许任何后缀访问
                $this->path = preg_replace('/\.' . $this->ext() . '$/i', '', $pathinfo);
            }
        }
        return $this->path;

    }

    /**
     * 当前后缀
     * @return  string
     * @todo  pathinfo()
     */
    public function ext() {
        return pathinfo($this->pathinfo(),PATHINFO_EXTENSION);
    }

    /**
     * 请求的资源类型
     * @return false | string
     */
    public function type() {
        $accept = $this->server('HTTP_ACCEPT');
        if (empty($accept)) {
            return false;
        }
        foreach ($this->mimeType as $type => $value) {
            $array = explode(",", $value);
            foreach ($array as $k => $v) {
                if (stristr($accept, $v)) {
                    return $type;
                }
            }
        }
        return false;
    }

    /**
     * 设置minitype
     * @param  string|array $type 资源类型
     * @param  string $val  资源类型值
     * @return void       
     */
    public function miniType($type, $val = '') {
        if (is_array($type)) {
            $this->miniType = array_merge($this->miniType,$type);
        } else {
            $this->miniType[$type] = $value;
        }
    }

    /**
     * 获取或者设置当前方法
     * @param  boolean $method true 获取  
     * @return string          
     */
    public function method($method = false) {
        if (true === $method) {
            return isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
        } elseif (!$this->method) {
            if (isset($_POST[Config::get('app.var_method')])) {
                $this->method = strtoupper($_POST[Config::get('app.var_method')]);
                $this->{$this->method}($_POST);
            } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
               $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            } else {
                $this->method = isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
            }
        }
        return $this->method;
    }

    /**
     * 获取参数
     * @param  string $name    参数名
     * @param  mixed $default 
     * @param  string|filter $filter  
     * @return mixed          
     */
    public function params($name = "", $default = null, $filter = '') {
        if (empty($this->param)) {
            $method = $this->method(true);
            switch ($method) {
                case 'POST':
                    $vars = $this->post(false);
                    break;
                case 'PUT': 
                case 'DELETE':
                case 'PATCH':
                    $vars = $this->put(false);
                    break;
                default:
                    $vars = [];
            }
            $this->param = array_merge($this->get(false), $vars, $this->route(false));
        }
        return $this->input($this->param, $name, $default, $filter);
    }

    /**
     * 设置或者获取路由参数
     * @param  string $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed    
     */
    public function route($name = '', $default = null, $filter = "") {
        if (is_array($name)) {
            $this->param = [];
            return $this->route = array_merge($this->route, $name);
        }
        return $this->input($this->route, $name, $default, $filter);
    }

    /**
     * 获取路由信息
     * @param  array  $route [description]
     * @return array        
     */
    public function routeInfo($route = []) {
        if (!empty($toute)) {
            $this->routeInfo = $route;
        } else {
            return $this->routeInfo;
        }
    }   

    /**
     * 获取或者设置控制器
     * @param  string $controller 
     * @return              
     */
    public function controller($controller = null) {
        if (!is_null($controller)) {
            $this->controller = $controller;
            return $this;
        } else {
            return $this->controller ?: '';
        }
    }

    /**
     * 获取或者设置action
     * @param  string $action 
     * @return          
     */
    public function action($action = '') {
        if (!is_null($action)) {
            $this->action = $action;
            return $this;
        } else {
            return $this->action ?: '';
        }
    }

    /**
     * 获取或者设置当前的语言
     * @param  [type] $lang [description]
     * @return [type]       [description]
     */
    public function langset($lang = null) {
        if (!is_null($lang)) {
            $this->langset = $lang;
            return $this;
        } else {
            return $this->langset ?: '';
        }
    }
    
    /**
     * 设置或者获取当前请求的content
     * @access public
     * @return string
     */
    public function getContent() {
        if (is_null($this->content)) {
            $this->content = $this->input;
        }
        return $this->content;
    }

    /**
     * 获取当前请求的php://input
     * @access public
     * @return string
     */
    public function getInput() {
        return $this->input;
    }


    /**
     * 获取或者设置get参数
     * @param  string|array $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed          
     */
    public function get($name = '', $default = null, $filter = '') {
        if (empty($this->get)) {
            $this->get = $_GET;
        }
        if (is_array($name)) {
            $this->param = [];
            return $this->get = array_merge($this->get,$name);
        }
        return $this->input($this->get, $name ,$default, $filter);
    }

    /**
     * 获取或者设置post参数
     * @param  string|array $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed          
     */
    public function post($name = '', $default = null, $filter = '') {
        if (empty($this->post)) {
            $content = $this->input;
            if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
                $this->post = (array)json_decode($content, true);
            } else {
                $this->post = $_POST;
            }
        }
        if (is_array($name)) {
            $this->param = [];
            return $this->post = array_merge($this->post, $name);
        }
        return $this->input($this->post, $name, $default, $filter);
    }

    /**
     * 获取或者设置put参数
     * @param  string|array $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed          
     */
    public function put($name = '', $default = null, $filter = '') {
        if (empty($this->put)) {
            $content = $this->input;
            if (false !== strpos($this->contentType(), 'application/json')) {
                $this->put = (array)json_decode($content, true);
            } else {
                parse_str($content, $this->put);
            }
        }
        if (is_array($name)) {
            $this->param = [];
            return $this->put = is_null($this->put) ? $name : array_merge($this->put, $name);
        }
        return $this->input($this->put, $name, $default, $filter);
    }

    /**
     * 获取或者设置delete参数
     * @param  string|array $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed          
     */
    public function delete($name = '', $default = null, $filter = '') {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取或者设置patch参数
     * @param  string|array $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed          
     */
    public function patch($name = '', $default = null, $filter = '') {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取request变量
     * @param string        $name 数据名称
     * @param string        $default 默认值
     * @param string|array  $filter 过滤方法
     * @return mixed
     */
    public function request($name = '', $default = null, $filter = '') {
        if (empty($this->request)) {
            $this->request = $_REQUEST;
        }
        if (is_array($name)) {
            $this->param = [];
            return $this->request = array_merge($this->request, $name);
        }
        return $this->input($this->request, $name, $default, $filter);
    }

    /**
     * 获取session数据
     * @access public
     * @param string|array  $name 数据名称
     * @param string        $default 默认值
     * @param string|array  $filter 过滤方法
     * @return mixed
     * @todo
     */
    public function session($name = '', $default = null, $filter = '') {
        if (empty($this->session)) {
            $this->session = Session::get();
        }
        if (is_array($name)) {
            return $this->session = array_merge($this->session, $name);
        }
        return $this->input($this->session, $name, $default, $filter);
    }

    /**
     * 获取cookie参数
     * @access public
     * @param string|array  $name 数据名称
     * @param string        $default 默认值
     * @param string|array  $filter 过滤方法
     * @return mixed
     * @todo
     */
    public function cookie($name = '', $default = null, $filter = '') {
        if (empty($this->cookie)) {
            $this->cookie = Cookie::get();
        }
        if (is_array($name)) {
            return $this->cookie = array_merge($this->cookie, $name);
        } elseif (!empty($name)) {
            $data = Cookie::has($name) ? Cookie::get($name) : $default;
        } else {
            $data = $this->cookie;
        }

        $filter = $this->getFilter($filter, $default);
        if (is_array($data)) {
            array_walk_recursive($data, [$this,'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }
        return $data;
    }

    /**
     * 获取服务器信息
     * @param  string $name    
     * @param  null|string $default 
     * @param  string|array $fileter 
     * @return mixed         
     */
    public function server($name = "", $default = null , $filter = "") {
        if (empty($this->server)) {
            $this->server = $_SERVER;
        }
        if (is_array($name)) {
            return $this->server = array_merge($this->server,$name);
        }
        return $this->input($this->server, false === $name ? false : strtoupper($name), $default, $filter);
    }
    
    /**
     * 获取或者设置请求头
     * @param  string|array $name    
     * @param  string $default 
     * @return string|array          
     */
    public function header($name = '', $default = null) {
        if (empty($this->header)) {
            $header = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $this->server ?: $_SERVER;
                foreach ($server as $key => $value) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key = str_replace("_", '-', strtolower(substr($key, 5)));
                        $header[$key] = $value;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }

            $this->header = array_change_key_case($header);
        }
        if (is_array($name)) {
            return $this->header = array_marge($this->header, $name);     
        }
        if ('' === $name) {
            return $this->header;
        }
        $name = str_replace("_", "-", strtolower($name));
        return isset($this->header[$name]) ?: $default;
    }

    /**
     * 获取变量
     * @param  array  $data    
     * @param  string|false $name    
     * @param  mixed $default 
     * @param  string|array $filter  
     * @return mixed
     */
    public function input($data = [], $name = "", $default = null, $filter = "") {
        if (false === $name) {
            return $data;
        }
        $name = (string)$name;
        if ("" != $name) {
            // 解析name
            if (strpos($name, "/")) {
                list($name,$type) = explode('/', $name);
            } else {
                $type = "s";
            }
            foreach (explode('.', $name) as $value) {
                if (isset($data[$value])) {
                    $data = $data[$value];
                } else {
                    return $default;
                }
            }
            if (is_object($data)) {
                return $data;
            }
        }

        //过滤
        $filter = $this->getFilter($filter, $default);
        if (is_array($data)) {
            array_walk_recursive($data, [$this,'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }
        return $data;
    }

    /**
     * 返回http
     * @return string 
     */
    public function scheme() {
        return $this->isSsl() ? "https" : "http";
    }

    /**
     * 当前请求的host
     * @access public
     * @return string
     */
    public function host() {
        if (isset($_SERVER['HTTP_X_REAL_HOST'])) {
            return $_SERVER['HTTP_X_REAL_HOST'];
        }
        return $this->server('HTTP_HOST');
    }

    /**
     * port参数
     * @return int 
     */ 
    public function port() {
        return $this->server('SERVER_PORT');
    }

    /**
     * 当前请求URL地址中的query参数
     * @access public
     * @return string
     */
    public function query() {
        return $this->server('QUERY_STRING');
    }
    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @return string
     */
    public function contentType() {
        $contentType = $this->server('CONTENT_TYPE');
        if ($contentType) {
            if (strpos($contentType, ';')) {
                list($type) = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }
        return '';
    }

    /**
     * 设置或者获取filter
     * @param  string|null $filter 
     * @return null|string         
     */
    public function filter($filter = null) {
        if (is_null($filter)) {
            return $this->filter;
        } else {
            $this->filter = $filter;
        }
    }

    /**
     * 获取过滤参数
     * @param  $filter
     * @param  $default 
     * @return 
     */
    public function getFilter($filter, $default) {
        if (is_null($filter)) {
            $filter = array();
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && false === strpos($filter, "/")) {
                $filter = explode(",", $filter);
            } else {
                $filter = (array) $filter;
            }
        }
        $filter[] = $default;
        return $filter;
    }

    /**
     * 过滤值
     * @param  mixed&$value
     * @param  mixed $key    
     * @param  array $filter 
     * @return mixed
     */
    public function filterValue(&$value, $key, $filters) {
        $default = array_pop($filters);
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $value = call_user_func($filter, $value);
            } elseif (is_scalar($value)) {
                if (false !== strpos($filter, "/")) {
                    if (!preg_match($filter, $value)) {
                        $value = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    $value = filter_var($value, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $value) {
                        $value = $default;      
                        break;
                    }
                }
            } 
        }
        return $this->filterExp($value);
    }

    /**
     * 过滤表单中的表达式
     * @param string $value
     * @return void
     */
    public function filterExp(&$value){
        // 过滤查询特殊字符
        if (is_string($value) && preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT LIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
            $value .= ' ';
        }
        // TODO 其他安全过滤
    }

    /**
     * 请求参数
     * @param  string  $name       
     * @param  string  $type       
     * @param  boolean $checkEmpty 
     * @return bool             
     */
    public function has($name, $type = 'params', $checkEmpty = false) {
        //获取参数
        if (empty($this->type)) {
            $params = $this->$type();
        } else {
            $params = $this->$type;
        }

        foreach (explode(".", $name) as $value) {
            if (isset($params[$value])) {
                $param = $params[$value];
            } else {
                return false;
            }
        }
        return ($checkEmpty && '' === $param) ? false : true;
    }

    /**
     * 获取指定参数
     * @param  string $name 
     * @param  string $type 
     * @return array
     */
    public function only($name, $type = 'params') {
        $params = $this->$type();
        if (is_string($name)) {
            $name = explode(",", $name);
        }
        $item = [];
        foreach ($name as $value) {
            if (isset($params[$value])) {
                $item[$value] = $params[$value];
            }
        }
        return $item;
    }

    /**
     * 排除指定元素
     * @param  sting $name 
     * @param  string $type 
     * @return array       
     */
    public function except($name, $type = 'params') {
        $params = $this->$type();

        if (is_string($name)) {
            $name = explode(",", $name);
        }
        foreach ($name as $value) {
            if (isset($params[$value])) {
                unset($params[$value]);
            }
        }
        return $params;
    }

    /**
     * 放回ip地址
     * @return [type] [description]
     */     
    public function ip() {
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip); 
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }


    /**
     * 是否get请求
     * @return boolean 
     */
    public function isGet() {
        return strtoupper($this->method()) == 'GET';
    }

    /**
     * 是否post请求
     * @return boolean 
     */
    public function isPost() {
        return strtoupper($this->method()) == 'POST';
    }

    /**
     * 是否ajax请求
     * @return boolean 
     */
    public function isAjax() {
        $value  = $this->server('HTTP_X_REQUESTED_WITH', '', 'strtolower');
        return 'xmlhttprequest' == strtolower($value) ? true : false;
    }
    /**
     * 是否put请求
     * @return boolean 
     */
    public function isPut() {
        return strtoupper($this->method()) == 'PUT';
    } 

    /**
     * 是否delete请求
     * @return boolean 
     */
    public function isDelete() {
        return strtoupper($this->method()) == "DELETE";
    }

    /**
     * 是否head请求
     * @return boolean 
     */
    public function isHead() {
        return strtoupper($this->method()) == 'HEAD';
    }

    /**
     * 是否patch请求
     * @return boolean 
     */
    public function isPatch() {
        return strtoupper($this->method()) == 'PATCH';
    }

    /**
     * 是否post请求
     * @return boolean 
     */
    public function isOptions() {
        return strtoupper($this->method()) == 'OPTIONS';
    }

    /**
     * `是否cli
     * @return boolean 
     */
    public function isCli() {
        return strtoupper(PHP_SAPI) == 'CLI';
    }

    /**
     * 是否为cgi
     * @return boolean 
     */
    public function isCgi() {
        return strpos(PHP_SAPI, 'cgi') ===0;
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile() {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl() {
        $server = array_merge($_SERVER, $this->server);
        if (isset($server['HTTPS']) && ('1' == $server['HTTPS'] || 'on' == strtolower($server['HTTPS']))) {
            return true;
        } elseif (isset($server['REQUEST_SCHEME']) && 'https' == $server['REQUEST_SCHEME']) {
            return true;
        } elseif (isset($server['SERVER_PORT']) && ('443' == $server['SERVER_PORT'])) {
            return true;
        } elseif (isset($server['HTTP_X_FORWARDED_PROTO']) && 'https' == $server['HTTP_X_FORWARDED_PROTO']) {
            return true;
        } elseif (Config::get('https_agent_name') && isset($server[Config::get('https_agent_name')])) {
            return true;
        }
        return false;
    }
}
