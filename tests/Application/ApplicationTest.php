<?php
namespace Tests\Application;

use PHPUnit\Framework\TestCase;

class ApplicationdTest extends TestCase {
    public function testSuccess() {
        $this->assertSame('success', 'success');
    }
}