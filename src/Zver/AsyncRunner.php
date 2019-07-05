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
    protected $queue;
    protected $pool;
    protected $concurrency = 20;//max threads at same time
    protected $concurrencyTimeout;// do not run new thread before that timeout reached

    public static function create($checkRequirements = true)
    {
        if ($checkRequirements && !Pool::isSupported()) {
            throw new Exception('Async run is not supported, required extensions pcntl and posix');
        }
        return new static();
    }

    protected function __construct()
    {
        $this->queue = [];
        $this->pool = Pool::create();
    }

    public function addTask(callable $callback, callable $onSuccess, callable $onError)
    {
        if (is_null($this->concurrencyTimeout)) {
            throw new Exception('Set concurency timeout first');
        }

        $this->queue[] = [$callback, $onSuccess, $onError];
        return $this;
    }

    public function run()
    {
        $this->pool->wait();
    }

    public function setConcurrency(int $concurrency)
    {
        $this->concurrency = $concurrency;
        return $this;
    }

    public function getConcurrency()
    {
        return $this->concurrency;
    }

}