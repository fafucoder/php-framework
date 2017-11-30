<?php 
namespace core\system;
use core\system\Config;
class Model extends \PDO {
	public function __construct(){
		$database = Config::all("database");
		$dsn="{$database['DB_TYPE']}:host={$database['DB_HOST']};dbname={$database['DATABASE']}";
		try{
			parent::__construct($dsn,$database["USER"],$database['PASSWORD']);
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}
}