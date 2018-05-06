<?php 
namespace System;

class Response {
    /**
     * 数据
     * @var mixed
     */
    public $data;

    /**
     * 当前content-type
     * @var string
     */
    public $contentType = 'text/html';

    /**
     * 当前字符集
     * @var string
     */
    public $charset = 'utf-8';

    /**
     * 当前状态码
     * @var integer
     */
    public $code = 200;

    /**
     * 输出选项
     * @var array
     */
    public $options = [];

    /**
     * 头部信息
     * @var array
     */
    public $header = [];

    /**
     * 当前内容
     * @var null
     */
    public $content = null;

    /**
     * 构造函数
     * @param mixed $data    输出数据
     * @param int   $code
     * @param array $header
     * @param array $options 输出参数
     */
    public function __construct($data = '', $code = 200, array $header = [], $options = []) {
        $this->data($data);
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->contentType($this->contentType, $this->charset);
        $this->header = array_merge($this->header, $header);
        $this->code = $code;
    }

    /**
     * 创建Response对象
     * @param mixed  $data    输出数据
     * @param string $type    输出类型
     * @param int    $code
     * @param array  $header
     * @param array  $options 输出参数
     * @return Response|JsonResponse|ViewResponse|XmlResponse|RedirectResponse|JsonpResponse
     */
    public static function create($data = '', $type = '', $code = 200, array $header = [], $options = []) {
        $type = empty($type) ? 'null' : strtolower($type);

        $class = false !== strpos($type, '\\') ? $type : '\\think\\response\\' . ucfirst($type);
        if (class_exists($class)) {
            $response = new $class($data, $code, $header, $options);
        } else {
            $response = new static($data, $code, $header, $options);
        }

        return $response;
    }

    /**
     * 发送数据到客户端
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function send() {
        $data = $this->getContent();

        if (!headers_sent() && !empty($this->header)) {
            http_response_code($this->code);
            foreach ($this->header as $name => $val) {
                if (is_null($val)) {
                    header($name);
                } else {
                    header($name . ':' . $val);
                }
            }
        }

        echo $data;

        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
    }

    /**
     * 处理数据
     * @param mixed $data 要处理的数据
     * @return mixed
     */
    protected function output($data) {
        return $data;
    }

    /**
     * 输出的参数
     * @param mixed $options 输出参数
     * @return $this
     */
    public function options($options = []) {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * 输出数据设置
     * @param mixed $data 输出数据
     * @return $this
     */
    public function data($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * 设置响应头
     * @param string|array $name  参数名
     * @param string       $value 参数值
     * @return $this
     */
    public function header($name, $value = null) {
        if (is_array($name)) {
            $this->header = array_merge($this->header, $name);
        } else {
            $this->header[$name] = $value;
        }
        return $this;
    }

    /**
     * 设置页面输出内容
     * @param $content
     * @return $this
     */
    public function content($content) {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([$content, '__toString'])) {
            throw new \Exception(sprintf('variable type error： %s', gettype($content)));
        }
        $this->content = (string) $content;
        return $this;
    }

    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return $this
     */
    public function code($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * LastModified
     * @param string $time
     * @return $this
     */
    public function lastModified($time) {
        $this->header['Last-Modified'] = $time;
        return $this;
    }

    /**
     * Expires
     * @param string $time
     * @return $this
     */
    public function expires($time) {
        $this->header['Expires'] = $time;
        return $this;
    }

    /**
     * ETag
     * @param string $eTag
     * @return $this
     */
    public function eTag($eTag) {
        $this->header['ETag'] = $eTag;
        return $this;
    }

    /**
     * 页面缓存控制
     * @param string $cache 状态码
     * @return $this
     */
    public function cacheControl($cache) {
        $this->header['Cache-control'] = $cache;
        return $this;
    }

    /**
     * 页面输出类型
     * @param string $contentType 输出类型
     * @param string $charset     输出编码
     * @return $this
     */
    public function contentType($contentType, $charset = 'utf-8') {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;
        return $this;
    }

    /**
     * 获取头部信息
     * @param string $name 头部名称
     * @return mixed
     */
    public function getHeader($name = '') {
        if (!empty($name)) {
            return isset($this->header[$name]) ? $this->header[$name] : null;
        } else {
            return $this->header;
        }
    }

    /**
     * 获取原始数据
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 获取输出数据
     * @return mixed
     */
    public function getContent() {
        if (null == $this->content) {
            $content = $this->output($this->data);

            if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                $content,
                '__toString',
            ])
            ) {
                throw new \InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
            }

            $this->content = (string) $content;
        }
        return $this->content;
    }

    /**
     * 获取状态码
     * @return integer
     */
    public function getCode() {
        return $this->code;
    }

    public function jsonReturn() {

    }

    public function Redirect() {
        
    }
}
