<?php

use Zver\AsyncRunner;
use Zver\AsyncRunnerTestTask;

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

    public function assertDurationLessThenOrEquals($callback, $duration)
    {
        $runnedAt = time();
        $callback();
        $callbackDuration = round(time() - $runnedAt, 2);
        $this->assertTrue($duration >= $callbackDuration);
    }

    public function testRunTwice()
    {
        $this->expectException('Exception');
        $runner = new AsyncRunner();
        $runner->runAndWait();
        $runner->runAndWait();
    }

    protected function getSyncResultIds(int $count)
    {
        return array_slice([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 0, $count);
    }

    public function testRunQueueWithoutTimeout()
    {
        $this->assertDurationLessThenOrEquals(
            function () {
                $count = 10;
                $runner = new AsyncRunner();
                for ($i = 0; $i < $count; $i++) {
                    $runner->addTask(new AsyncRunnerTestTask($i));
                }
                $results = $runner->runAndWait();
                $this->assertNotEmpty($results);
                $ids = array_map(function (AsyncRunnerTestTask $task) {
                    return $task->getId();
                }, $results);
                $this->assertSame(count($results), $count);
                $this->assertNotEquals($ids, $this->getSyncResultIds($count));
            }, 6);
    }

    public function testRunQueueWithTimeout()
    {
        $count = 10;
        $runner = new AsyncRunner(1);
        for ($i = 0; $i < $count; $i++) {
            $runner->addTask(new AsyncRunnerTestTask($i));
        }
        $results = $runner->runAndWait();
        $this->assertNotEmpty($results);
        $ids = array_map(function (AsyncRunnerTestTask $task) {
            return $task->getId();
        }, $results);
        $this->assertSame(count($results), $count);
        $this->assertNotEquals($ids, $this->getSyncResultIds($count));
    }

    public function testRunQueueWithTimeoutGreaterExecutionTime()//sync simulation
    {
        $count = 5;
        $runner = new AsyncRunner(10);
        for ($i = 0; $i < $count; $i++) {
            $runner->addTask(new AsyncRunnerTestTask($i));
        }
        $results = $runner->runAndWait();
        $this->assertNotEmpty($results);
        $ids = array_map(function (AsyncRunnerTestTask $task) {
            return $task->getId();
        }, $results);
        $this->assertSame(count($results), $count);
        $this->assertEquals($ids, $this->getSyncResultIds($count));
    }

}