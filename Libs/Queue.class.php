<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Queue\Libs\Queues\DatabaseQueue;

abstract class Queue {

    /**
     * @var Queue
     */
    private static $queue;

    /**
     *
     */
    static function getInstance() {
        if(empty(static::$queue)){
            //目前默认是DB
            return static::$queue = new DatabaseQueue();
        }
    }

//    $connection

    /**
     * 链接
     */
    protected function connect() {

    }


    public function push($queue = '', \Job $job) {

        return $this;
    }

    /**
     * @param string $queue
     * @return \Job|null
     */
    public function pop($queue = '') {
        return null;
    }

    public function size() {

    }

}