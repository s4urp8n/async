<?php

use Zver\AsyncRunner;

class AsyncRunnerTest extends PHPUnit\Framework\TestCase
{
    public function testIsSupported()
    {
        $this->assertTrue(\Spatie\Async\Pool::isSupported());
    }
}