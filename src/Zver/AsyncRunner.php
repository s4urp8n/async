<?php
/**
 * Created by PhpStorm.
 * User: s4urp
 * Date: 02.07.2019
 * Time: 12:17
 */

namespace Zver;

use Exception;
use Spatie\Async\Pool;

class AsyncRunner
{

    protected const TASK_CALLBACK = 'CALLBACK';
    protected const TASK_SUCCESS  = 'SUCCCESS';
    protected const TASK_FAIL     = 'FAIL';

    protected $runnedAtTimestamp = false;

    protected $queue = [];

    protected $pool;

    protected $concurrency = 20;

    protected $concurrencyTimeout;

    public function __construct(int $taskRunPauseSeconds = 0, int $maxTaskAtSameTime = 20, int $killTaskAfterSeconds = 0)
    {
        if (!Pool::isSupported()) {
            throw new Exception('Async run is not supported, required extensions PCNTL and POSIX');
        }
        $this->concurrencyTimeout = $taskRunPauseSeconds;
        $this->pool = Pool::create()
                          ->concurrency($maxTaskAtSameTime)
                          ->timeout($killTaskAfterSeconds);
    }

    public function addTask(callable $callback, callable $onSuccess, callable $onError)
    {
        $this->queue[] = [
            static::TASK_CALLBACK => $callback,
            static::TASK_SUCCESS  => $onSuccess,
            static::TASK_FAIL     => $onError,
        ];
        return $this;
    }

    protected function isQueueEmpty(): bool
    {
        return count($this->queue) == 0;
    }

    protected function addTasksFromQueue()
    {
        foreach ($this->queue as $index => $task) {
            if ($this->isTimeToRunTask($index)) {
                $this->pool->add($task[static::TASK_CALLBACK])
                           ->then($task[static::TASK_SUCCESS])
                           ->catch($task[static::TASK_FAIL]);
            }
        }
    }

    protected function isTimeToRunTask($taskIndex): bool
    {
        $taskRunOffset = $taskIndex * $this->concurrencyTimeout;
        $runTime = $this->runnedAtTimestamp + $taskRunOffset;
        return time() >= $runTime;
    }

    protected function run()
    {
        while (!$this->isQueueEmpty()) {
            $this->addTasksFromQueue();
            $this->pool->wait();
        }
    }

    public function runAndWait()
    {
        //runner can be runner once!
        if ($this->runnedAtTimestamp) {
            throw new Exception('Runner already executed');
        }
        $this->runnedAtTimestamp = time();
        $this->run();
    }

}