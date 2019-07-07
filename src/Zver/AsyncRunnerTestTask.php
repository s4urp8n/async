<?php
/**
 * Created by PhpStorm.
 * User: s4urp
 * Date: 07.07.2019
 * Time: 13:54
 */

namespace Zver;

class AsyncRunnerTestTask extends AsyncTask
{
    protected $id;
    protected $n;

    public function configure()
    {

    }

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function run()
    {
        $n = rand(1, 5);
        $this->n = $n;
        sleep($n);
        return $this;
    }

    public function getN()
    {
        return $this->n;
    }

    public function getId()
    {
        return $this->id;
    }
}