<?php 

\System\Request::create('http://192.168.1.200/basetest/server.php/index/index?id=2&name=lrc');
$request = \System\Request::instance();
var_dump($request);

/**
 * 测试domain
 */
var_dump("get domain: {$request->domain()}");
$request->domain('http://www.baidu.com');
var_dump("get changed domain: {$request->domain()}");

/**
 * 测试url
 */
var_dump("get url: {$request->url()}");
var_dump("get full url: {$request->url(true)}");
$request->url('/thinkphp/');
var_dump("get changed url: {$request->url()}");

/**
 * 测试baseUrl
 */
var_dump("get baseurl: {$request->baseUrl()}");
var_dump("get full baseurl: {$request->baseUrl(true)}");
$request->baseUrl('/miniphp/');
var_dump("get changed baseUrl: {$request->baseUrl()}");

/**
 * 测试basefile
 */
var_dump("get baseFile : {$request->baseFile()}");
var_dump("get full baseFile: {$request->baseFile(true)}");
$request->baseFile('home.php');
var_dump("get changed baseFile: {$request->baseFile()}");

/**
 * 测试root
 */
var_dump("get root : {$request->root()}");
var_dump("get full root: {$request->root(true)}");
$request->root('home.php');
var_dump("get changed root: {$request->root()}");

/**
 * `测试类型
 */
var_dump($request->method(true));

var_dump($request->put());
var_dump($request->get());
var_dump($request->post());
var_dump($request->params());
var_dump($request->request());
var_dump($request->header());
var_dump($request->has('id'));
var_dump($request->only('id,name'));
var_dump($request->except('name'));
var_dump($request->ip());
var_dump($request->isAjax());
var_dump($request->isMobile());
var_dump($request->host());
var_dump($request->scheme());
var_dump($request->query());