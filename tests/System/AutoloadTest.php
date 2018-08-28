<?php
namespace Tests\System;

use PHPUnit\Framework\TestCase;

class AutoloadTest extends TestCase {
    public function testSuccess() {
        $this->assertSame('hello world', 'hello world');
    }
}