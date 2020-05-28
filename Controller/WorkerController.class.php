<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Queue\Libs\Queue;
use Queue\Libs\Utils;
use Queue\Libs\Worker;
use Queue\Libs\WorkerOptions;

/**
 * 任务管理
 */
class WorkerController extends QueueController {


    /**
     * 监听任务队列
     */
    public function run() {
        //指定开启的队列,从左到右，优先级别由高到低
        $queue = I('get.queue', '');

        $queue_manager = Queue::getInstance();
        $workerOptions = new WorkerOptions(C('QUEUE_SLEEP'), C('QUEUE_MAX_RETRY'));
        $worker = new Worker($queue_manager, $workerOptions);

        $worker->run($queue);
    }

    /**
     * 平滑停止监听任务队列
     */
    public function stop() {
        cache('queue_work_stop', '1');
        Utils::log('Send STOP signal.');
    }

}