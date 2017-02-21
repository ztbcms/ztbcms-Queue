<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Queue\Libs\Queue;
use Queue\Libs\Worker;

class WorkerController extends QueueController {


    /**
     * 入口
     */
    public function run() {
        //指定开启的队列
        $queue = I('get.queue', '');

        $queue_manager = Queue::getInstance();
        $worker = new Worker($queue_manager);
        $worker->run($queue);
    }
}