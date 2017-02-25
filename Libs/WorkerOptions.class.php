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
    public $sleep = 5;

    /**
     * 最大重试次数
     * @var int
     */
    public $maxRetry = 3;

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


}