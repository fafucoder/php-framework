<?php
namespace Tests\System\Core;

use PHPUnit\Framework\TestCase;
use Framework\Core\Loader;

class LoaderTest extends TestCase {
	public function testLoader() {
		$this->loader = new Loader();
		var_dump($this->loader);exit;
	}
}