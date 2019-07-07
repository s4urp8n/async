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

    protected $runnedAtTimestamp = false;
    protected $results           = [];
    protected $taskTimeout       = 0;
    protected $queue             = [];
    /**
     * @var Pool $pool
     */
    protected $pool;
    protected $concurrency;
    protected $concurrencyTimeout;

    public function __construct(int $taskRunPauseSeconds = 0, int $maxTaskAtSameTime = 20, int $killTaskAfterSeconds = 3600)
    {
        if (!Pool::isSupported()) {
            throw new Exception('Async run is not supported, required extensions PCNTL and POSIX');
        }
        $this->taskTimeout = $killTaskAfterSeconds;
        $this->concurrency = $maxTaskAtSameTime;
        $this->concurrencyTimeout = $taskRunPauseSeconds;
    }

    protected function createPool()
    {
        $this->pool = Pool::create()
                          ->concurrency($this->concurrency)
                          ->timeout($this->taskTimeout);
    }

    public function addTask(AsyncTask $task)
    {
        $this->queue[] = $task;
        return $this;
    }

    protected function isQueueEmpty(): bool
    {
        foreach ($this->queue as $value) {
            if (!is_null($value)) {
                return false;
            }
        }
        return true;
    }

    protected function addTasksFromQueue()
    {
        $this->createPool();
        foreach ($this->queue as $index => $task) {
            if (is_null($task)) {
                continue;
            }
            if ($this->isTimeToRunTask($index)) {
                $this->pool->add($task);
                $this->unsetTask($index);
            }
        }
    }

    protected function unsetTask($index)
    {
        $this->queue[$index] = null;
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
            foreach ($this->pool->wait() as $result) {
                $this->results[] = $result;
            }
        }
        return $this->results;
    }

    public function runAndWait()
    {
        //runner can be runner once!
        if ($this->runnedAtTimestamp) {
            throw new Exception('Runner already executed');
        }
        $this->runnedAtTimestamp = time();
        return $this->run();
    }

}