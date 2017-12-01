<?php 
namespace core\system;
use core\system\Config;
use Medoo\Medoo;
class Model extends Medoo {
	public function __construct(){
		$option = Config::all("database");
		parent::__construct($option);

		/*		
		$dsn="{$database['DB_TYPE']}:host={$database['DB_HOST']};dbname={$database['DATABASE']}";
		try{
			parent::__construct($dsn,$database["USER"],$database['PASSWORD']);
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
		*/
	}
}