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
        $runner = new AsyncRunner();
        for ($i = 0; $i < 10; $i++) {

            $callback = function () use ($i) {
                $n = rand(1, 5);
                sleep($n);
                return $i;
            };

            $success = function ($result) {
                return $result;
            };

            $failed = function (Throwable $e) {
                throw $e;
            };

            $timeout = function () use ($i) {

            };

            $runner->addTask($callback, $success, $failed, $timeout);
        }
        $syncExecutionResult = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $results = $runner->runAndWait();
        $this->assertNotEmpty($results);
        $this->assertNotEquals($results, $syncExecutionResult);
        $this->assertTrue(count($results) == 10);
        foreach ($syncExecutionResult as $value) {
            $this->assertTrue(in_array($value, $results));
        }
    }

    public function testRunQueueWithRunTimeout()
    {
        $runner = new AsyncRunner(2);
        for ($i = 0; $i < 10; $i++) {

            $callback = function () use ($i) {
                $n = rand(1, 5);
                sleep($n);
                return $i;
            };

            $success = function ($result) {
                return $result;
            };

            $failed = function (Throwable $e) {
                throw $e;
            };

            $timeout = function () use ($i) {

            };

            $runner->addTask($callback, $success, $failed, $timeout);
        }
        $syncExecutionResult = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $results = $runner->runAndWait();
        $this->assertNotEmpty($results);
        $this->assertNotEquals($results, $syncExecutionResult);
        $this->assertTrue(count($results) == 10);
        foreach ($syncExecutionResult as $value) {
            $this->assertTrue(in_array($value, $results));
        }
    }

    public function testRunQueueWithRunTimeoutIsReal()
    {
        $runner = new AsyncRunner(10); //sync simulation to check timeout to run realy executed
        for ($i = 0; $i < 10; $i++) {

            $callback = function () use ($i) {
                $n = rand(1, 5);
                sleep($n);
                return $i;
            };

            $success = function ($result) {
                return $result;
            };

            $failed = function (Throwable $e) {
                throw $e;
            };

            $timeout = function () use ($i) {

            };

            $runner->addTask($callback, $success, $failed, $timeout);
        }
        $syncExecutionResult = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $results = $runner->runAndWait();
        $this->assertNotEmpty($results);
        $this->assertEquals($results, $syncExecutionResult);
        $this->assertTrue(count($results) == 10);
        foreach ($syncExecutionResult as $value) {
            $this->assertTrue(in_array($value, $results));
        }
    }

}