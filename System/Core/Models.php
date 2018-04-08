<?php
namespace System;

use Medoo\Medoo;

class Model {

	/**
	 * config
	 * @var array
	 */
	public $config = array();

	/**
	 * 实例化
	 * @var [type]
	 */
	public static $instance;

	/**
	 * 数据库连接
	 * @var [type]
	 */
	public $connect;

	/**
	 * 设置表名
	 * @var [type]
	 */
	public $table;

	/**
	 * 更新的数据
	 * @var 
	 */		
	public $data;

	/**
	 * 选项
	 * @var array
	 */
	public $options = array();

	/**
	 * 连接的数据表
	 * @var [type]
	 */
	public $join;

	/**
	 * 查询字段
	 * @var [type]
	 */
	public $fields ;

	/**
	 * 这是个废弃方法为的是以后的改进！
	 * 唯一实例化
	 * @param string $config 数据库连接参数
	 */
	private static function Instance(array $config = []) {
		if (is_null(self::$instance)) {
			self::$instance = new self($config);
		}
		return self::$instance;
	}

	/**
	 * 构造函数
	 * @param array $config 数据库连接参数
	 */
	public function __construct(array $config = [], $table = null) {
		if ($config) {
			$this->config = array_merge($this->config, $config);
		} else {
			$this->config = Config::get('database');
		}
		$this->table = $table;
		$this->connnect = new Medoo($this->config);
	}

	/**
	 * table
	 * @param  string $table 
	 * @return $this        
	 */
	public function table($table) {
		$this->table = $table;
		return $this;
	}
	
	/**
	 * where语句
	 * @param  array   $where where条件
	 * @param  boolean $merge 
	 * @return $this
	 */
	public function where(array $where, $merge = true) {
		if ($merge) {
			$this->options['where'] = array_merge($this->options['where'], $where);
		} else {
			$this->options['where'] = $where;
		}
		return $this;
	}

	/**
	 * limit
	 * @param  mixed $offset 
	 * @param  mixed  $length
	 * @return $this
	 */
	public function limit($offset, $length = null) {
		if (is_null($length)) {
			$this->options['limit'] = $offset;
		} else {
			$this->options['limit'] = array($offset, $length);
		}
		return $this;
	}

	/**
	 * 分组
	 * @param  string|array $group 
	 * @return $this
	 */
	public function group($group){
		if (is_array($group)) {
			$this->options['group'] = array_merge($this->options['group'], $group); 
		} else {
			$this->options['group'] = $group;
		}
		return $this;
	}

	/**
	 * having条件
	 * @param  array|string $having 
	 * @return $this         
	 */
	public function having($having) {
		$this->options['having'] = $having;
		return $this;
	}

	/**
	 * join信息
	 * @param  [type] $join [description]
	 * @return [type]       [description]
	 */
	public function join($join) {
		if (is_array($join)) {
			$this->join = array_merge($this->join, $join);
		} else {
			$this->join = $join;
		}
		return $this;
	}

	/**
	 * 字段
	 * @param  string|array $fields 
	 * @return $this         
	 */
	public function fields($fields) {
		if (is_array($fields)) {
			$this->fields = array_merge($this->fields, $fields);
		} else {
			$this->fields = $fields
		}
		return $this;
	}

	/**
	 * 更新的数据
	 * @param  array|string $data 
	 * @return $this       
	 */
	public function data($data) {
		if (is_array($data)) {
			$this->data = array_merge($this->data, $data);
		} else {
			$this->data = $data;
		}
		return $this;
	}
	/**
	 * order
	 * @param  string|array $order 
	 * @return $this        
	 */
	public function order($order) {
		if (is_array($order)) {
			$this->options['order'] = array_merge($this->options['order'], $order); 
		} else {
			$this->options['order'] = $order;
		}
		return $this;
	}

	/**
	 * 返回insert id
	 * @return int
	 */
	public function id() {
		return $this->connect->id();
	}

	/**
	 * 数据库插入
	 * @return  $this
	 */
	public function insert() {
		$where = $this->parseOptions();
		$this->connect->insert($this->table, $where);
		return $this;
	}

	/**
	 * 数据库查询
	 * @return array 
	 */
	public function select() {
		$where = $this->parseOptions();
		return $this->connect->select($this->table, $this->join, $this->fields, $where);
	}

	/**
	 * 数据库更新
	 * @return interge 返回受影响的条数
	 */
	public function update() {
		$where = $this->parseOptions();
		return $this->connnect->update($this->table, $this->data, $where);
	}

	public function updateCount(){
		$where = $this->parseOptions();
		$count = $this->connnect->update($this->table, $this->data, $where);
		return $count->rowCount();
	}

	public function delete() {
		$where = $this->parseOptions();
		return $this->connect->delete($this->table, $where);
	}

	public function deleteCount() {
		$where = $this->parseOptions();
		$count = $this->connect->delete($this->table, $where);
		return $count->rowCount();
	}

	public function replace($table, $column, $where) {
		return $this->conncet->replace($table, $column, $where);
	}

	public function get() {
		$where = $this->parseOptions();
		return $this->connect->get($this->table, $this->join, $this->fields, $where);
	}

	public function has() {
		$where = $this->parseOptions();
		return $this->connect->has($this->table, $this->join, $where);

	}

	public function count() {
		$where = $this->parseOptions();
		return $this->connect->count($this->table, $this->join, $this->fields, $where);
	}

	public function max() {
		$where = $this->parseOptions();
		return $this->connect->max($this->table, $this->join, $this->fields, $where);
	}

	public function min() {
		$where = $this->parseOptions();
		return $this->connect->min($this->table, $this->join, $this->fields, $where);
	}

	public function avg() {
		$where = $this->parseOptions();
		return $this->connect->avg($this->table, $this->join, $this->fields, $where);
	}

	public function sum() {
		$where = $this->parseOptions();
		return $this->connect->avg($this->table, $this->join, $this->fields, $where);
	}

	public function action($callback) {
		return $this->connect->action($callback);
	}

	public function query($query) {
		return $this->connect->query($query);
	}

	/**
	 * 解析options中的内容
	 * @return [type] [description]
	 */
	protected function parseOptions() {
		foreach ($this->options as $key => $value) {
			if ('where' == strtolower($key)) {
				$where[] = $value;
			}
			$where[strtoupper($key)] = $value;
		}
		return $where;
	}

	/**
	 * 这是一个废弃方法为了是以后的改进
	 * @param  [type] $where [description]
	 * @param  [type] $parse [description]
	 * @return [type]        [description]
	 */
	private function wheres($where, $parse = null) {
		if (!is_null($prase) && is_string($where)) {
			if (!is_array($parse)) {
				$parse = func_get_args();
				array_shift($parse);
			}
			if (extension_loaded('mysqli')) {
				$parse = array_map('mysqli_real_escape_string', $parse);
			} elseif (extension_loaded('mysql')) {
				$parse = array_map('mysql_real_escape_string', $parse);
			}
			$where = vsprintf($where, $parse);
		} elseif (is_object($where)) {
			$where = get_object_vars($where);
		}
		if (isset($this->options['where'])) {
			$this->options['where'] = array_merge($this->options['where'], $where); 
		} else {
			$this->options['where'] = $where;
		}
	} 

	
}