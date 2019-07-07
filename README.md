# async

```
//create runner
$runner = new AsyncRunner();
for ($i = 0; $i < $count; $i++) {
    //add some task class
    $runner->addTask(new AsyncRunnerTestTask($i));
}
//wait for results
$results = $runner->runAndWait();
```

## constructor

```
public function __construct(int $taskRunPauseSeconds = 0, int $maxTaskAtSameTime = 20, int $killTaskAfterSeconds = 3600)
```

* taskRunPauseSeconds = 0, run next task after that timeout
* maxTaskAtSameTime = 20, max tasks at same time
* killTaskAfterSeconds = 3600, kill task after that timeout

## task class example

```
<?php
class AsyncRunnerTestTask extends AsyncTask
{
    public function configure()
    {
        //prepare for something
    }

    public function run()
    {
        //do something
    }
}
```

## install 

```
composer require zver/async
```

