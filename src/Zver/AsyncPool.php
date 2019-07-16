<?php
/**
 * Created by PhpStorm.
 * User: s4urp
 * Date: 16.07.2019
 * Time: 23:42
 */

namespace Zver;

use Spatie\Async\Pool;
use Spatie\Async\Process\SynchronousProcess;

class AsyncPool extends Pool
{

    public function isEmpty()
    {
        foreach ($this->inProgress as $process) {
            return false;
        }
        return true;
    }

    public function iterate()
    {
        foreach ($this->inProgress as $process) {
            if ($process->getCurrentExecutionTime() > $this->timeout) {
                $this->markAsTimedOut($process);
            }

            if ($process instanceof SynchronousProcess) {
                $this->markAsFinished($process);
            }
        }

        if (!$this->inProgress) {
            return;
        }

        usleep($this->sleepTime);
    }
}