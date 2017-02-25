<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

/**
 * worker 参数
 */
class WorkerOptions {

    /**
     * 队列空闲时休眠时间
     * @var int
     */
    private $sleep = 5;

    /**
     * 最大重试次数
     * @var int
     */
    private $maxRetry = 3;

    /**
     * WorkerOptions constructor.
     *
     * @param int $sleep
     * @param int $maxRetry
     */
    public function __construct($sleep, $maxRetry) {
        $this->sleep = $sleep;
        $this->maxRetry = $maxRetry;
    }

    /**
     * @return int
     */
    public function getSleep() {
        return $this->sleep;
    }

    /**
     * @param int $sleep
     */
    public function setSleep($sleep) {
        $this->sleep = $sleep;
    }

    /**
     * @return int
     */
    public function getMaxRetry() {
        return $this->maxRetry;
    }

    /**
     * @param int $maxRetry
     */
    public function setMaxRetry($maxRetry) {
        $this->maxRetry = $maxRetry;
    }


}