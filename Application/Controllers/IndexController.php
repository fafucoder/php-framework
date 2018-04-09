<?php
// namespace application\controllers;
//
// class IndexController extends \core\system\Base{
// 	public function Index()
// 	{
// 		$model = new \application\models\UserModel();
// 		$data = $model->select("user","*");
// 		dump($data);
// 	/*	$data = array(
// 			"id" =>null,
// 		    "username" => "lrc8",
// 		    "password" => "123",
// 		    "email" => null
// 		);
// 		$result = $model->insert("user",$data);
// 		dump($result);*/
//
// 		$this->assign('data',"hello world");
// 		$this->display("index/index.html");
// 	}
// }
namespace Application\Controllers;

class IndexController {
	public static function index(){
		demo();
	}
}
