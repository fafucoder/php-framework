<?php
namespace application\controllers;

class IndexController extends \core\system\Base{
	public function Index()
	{	
		$model = new \core\system\Model();
		$this->assign('data',"hello world");
		$this->display("index/index.html");
	}
}