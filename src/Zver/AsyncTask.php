<?php
/**
 * Created by PhpStorm.
 * User: s4urp
 * Date: 07.07.2019
 * Time: 13:23
 */

namespace Zver;

use Exception;
use Spatie\Async\Task;

class AsyncTask extends Task
{
    public function configure()
    {
        throw new Exception('Implement ' . __METHOD__);
    }

    public function run()
    {
        throw new Exception('Implement ' . __METHOD__);
    }
}