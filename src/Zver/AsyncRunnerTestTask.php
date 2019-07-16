<?php
/**
 * Created by PhpStorm.
 * User: s4urp
 * Date: 07.07.2019
 * Time: 13:54
 */

namespace Zver;

/**
 * Class AsyncRunnerTestTask
 * @package Zver
 */
class AsyncRunnerTestTask extends AsyncTask
{
    protected $id;
    protected $n;

    public function configure()
    {

    }

    /**
     * AsyncRunnerTestTask constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return $this|void
     */
    public function run()
    {
        $n = rand(1, 5);
        $this->n = $n;
        sleep($n);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getN()
    {
        return $this->n;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}