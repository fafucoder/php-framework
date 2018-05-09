<?php
namespace System;

class Build {

	protected static $controller = "
		<?php
			namespace Aplication\Controller;

			use System\Controller;

			class IndexController extends Controller {
				public function index(){
				}
			}

	";

	protected static $model = "
		<?php
			namespace Application Model;

			use System\Model;

			class ClassModel extends Model {
			}
	";

	/**
	 * 检查目录
	 * @return void
	 */
	public static function checkDir() {
		if (!is_dir(APP_PATH)) {
			self::buildApplication(APP_PATH);
		} elseif (!is_dir(ENVIRONMENT_PATH)) {
			self::buildEnvironment(ENVIRONMENT_PATH);
		} elseif (!is_dir(PUBLIC_PATH)) {
			self::buildPublic(PUBLIC_PATH);
		}
	}

	/**
	 * 创建引用目录
	 * @return void
	 */
	public static function buildApplication($application_path) {
		if (!is_dir($application_path)) {
			mkdir($application_path, 0755, true);
		}
		if (is_writeable($application_path)) {
			$dirs = array(
				COMMON_PATH,
				CONF_PATH,
				VIEW_PATH,
				CONTROLLER_PATH,
				MODEL_PATH,
				LOG_PATH,
				CACHE_PATH
			);
			$files = array(
				'functions.php' => COMMON_PATH,
				'config.php' => CONF_PATH,
				'database.php'	=> CONF_PATH,
				'route.php' => CONF_PATH,
				'setting.php' => CONF_PATH
			);

			foreach ($dirs as $dir) {
				if (!is_dir($dir)) {
					mkdir($dir, 0755, true);
				}
			}

			foreach ($files as $file => $path) {
				if (!file_exists($path . $file)) {
					file_put_contents($path . $file, "<?php");
				}
			}

			self::buildController(CONTROLLER_PATH);
			self::buildModel(MODEL_PATH);

		} else {
			header('Content-Type:text/html; charset=utf-8');
            exit('应用目录[' . $application_path . ']不可写，目录无法自动生成！<BR>请手动生成项目目录~');
		}
	}

	public static function buildEnvironment($environment_path) {
		if (!is_dir($environment_path)) {
			mkdir($environment_path, 0755, true);
		}
		$envs = array(
			'development.php',
			'product.php',
			'test.php'
		);
		foreach ($envs as $file) {
			if (!file($environment_path . $file)) {
				file_put_contents($environment_path . $file,
				"<?php
					return [
						//key => value
					];
				");
			}
		}
	}

	public static function buildPublic($public_path) {
		if (!is_dir($public_path)) {
			mkdir($public_path, 0755, true);
		}

		$publics = array(
			'css' => PUBLIC_PATH . 'assets/',
			'js' => PUBLIC_PATH . 'assets/',
			'images' => PUBLIC_PATH . 'assets/',
			'vendor' => PUBLIC_PATH
		);
		foreach ($publics as $dir => $path) {
			if (!is_dir($path . $dir)) {
				mkdir($path . $dir, 0755, true);
			}
		}
	}

	public static function buildController($controller_path) {
		file_put_contents($controller_path . 'IndexController.php',self::$controller);
	}

	public static function buildModel($model_path) {
		file_put_contents($model_path . 'IndexModel.php',self::$model);
	}

	/**
	 * 检测文件是否可写
	 * @param  string $file 文件名
	 * @return bool
	 */
	public static function is_writeable($file) {
		if (is_dir($file)) {
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === FALSE) {
				return FALSE;
			}
			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		} elseif (!is_file($file) || ($fp = @fopen($file, 'ab')) === FALSE) {
			return FALSE;
		}
		fclose($fp);
		return TRUE;
	}
}
