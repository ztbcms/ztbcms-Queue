<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Queue\Libs\Queue;
use Queue\Libs\Worker;
use Queue\Libs\WorkerOptions;

class WorkerController extends QueueController {


    /**
     * 入口
     */
    public function run() {
        //指定开启的队列,从左到右，优先级别由高到低
        $queue = I('get.queue', '');

        $queue_manager = Queue::getInstance();
        $worker = new Worker($queue_manager);
        $workerOptions = new WorkerOptions(C('QUEUE_SLEEP'));
        $worker->run($queue, $workerOptions);
    }
}