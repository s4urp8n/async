<?php

use Zver\AsyncRunner;

class AsyncRunnerTest extends PHPUnit\Framework\TestCase
{
    public function testIsSupported()
    {
        $runner = new AsyncRunner();
        $this->assertTrue(true);
    }

    public function testRun()
    {
        $runner = new AsyncRunner();
        $runner->runAndWait();
        $this->assertTrue(true);
    }

    public function testRunTwice()
    {
        $this->expectException('Exception');
        $runner = new AsyncRunner();
        $runner->runAndWait();
        $runner->runAndWait();
    }

    public function testRunQueueWithoutTimeout()
    {
        $callback = function () {
            $n = rand(1, 5);
            sleep($n);
            echo $n;
        };

        $empty = function () {
        };

        $runner = new AsyncRunner();
        for ($i = 0; $i < 10; $i++) {
            $runner->addTask($callback, $empty, $empty);
        }
        $runner->runAndWait();
        $this->assertTrue(true);
    }

}