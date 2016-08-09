<?php
	
namespace Tests;

use Yee\Yee;

class YeeTest extends \PHPUnit_Framework_TestCase {
	public function testConfigGettersAndSetters() {
		$app = new Yee();
		$app->config("test", "DummyValue");
		$this->assertEquals("DummyValue", $app->config("test"));
	}
}