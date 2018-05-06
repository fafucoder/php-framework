<?php
namespace Application\Controller;

use System\Controller;


class IndexController extends Controller {
	public $beforeAction = array(
		'index' => array('except' => 'add'),
	);

	public function index(){
		var_dump("欢迎来到我的框架");
	}

	public function add($id) {
		$this->assign('id', $id);
		$this->display('',array('name' => 'linrc'));
	}
}
