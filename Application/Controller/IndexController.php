<?php
namespace Application\Controller;

use System\Controller;


class IndexController extends Controller {

	public function index(){
		return $this->display();
	}

	public function add($id) {
		$this->assign('id', $id);
		$this->display('',array('name' => 'linrc'));
	}
}
