<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Queue\Jobs\ExcetionJob;
use Queue\Jobs\HugeJob;
use Queue\Jobs\UpdateJob;
use Queue\Libs\Queue;

class TestController extends QueueController {

    function push() {
        $job = new UpdateJob(time(), 'ztbcms');

        $queue = Queue::getInstance();
        $result = $queue->push('high', $job);
        var_dump($result);
    }

    //延时
    function pushWithDelete() {
        $job = new UpdateJob(time(), 'delete Job!');

        $queue = Queue::getInstance();
        $result = $queue->push('high', $job, 15); //
        var_dump($result);
    }

    function pop() {
        $queue = Queue::getInstance();
        $result = $queue->pop('high');
        var_dump($result);
    }

    function deleteJob() {
        $queue = Queue::getInstance();
        $queue->deleteJob(78);
    }

    function release() {
        $queue = Queue::getInstance();
        $job = new UpdateJob(6666, 'test..');
        $job->setId(79);
        $queue->release('mid', $job);
    }

    //模拟异常任务
    function pushExcetionJob() {
        $job = new ExcetionJob();

        $queue = Queue::getInstance();
        $result = $queue->push('high', $job);
        var_dump($result);

    }

    //模拟耗时任务
    function pushHugeJob() {
        $job = new HugeJob();

        $queue = Queue::getInstance();
        $result = $queue->push('low', $job);
        var_dump($result);

    }
}