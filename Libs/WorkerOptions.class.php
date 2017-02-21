<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

/**
 * worker 参数
 */
class WorkerOptions {

    public $sleep = 5;

    /**
     * WorkerOptions constructor.
     *
     * @param int $sleep
     */
    public function __construct($sleep) { $this->sleep = $sleep; }


}