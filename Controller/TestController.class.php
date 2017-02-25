<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\Base;
use Queue\Job\ExcetionJob;
use Queue\Job\UpdateJob;
use Queue\Libs\Queue;

class TestController extends Base {

    function push() {

        $job = new UpdateJob(time(), 'jayin');

        $queue = Queue::getInstance();
        $result = $queue->push('high', $job);
        var_dump($result);

//        var_dump(get_class_vars('Queue\Job\UpdateJob'));
    }

    function pop(){
        $queue = Queue::getInstance();
        $result = $queue->pop('high');
        var_dump($result);
    }

    function deleteJob(){
        $queue = Queue::getInstance();
        $queue->deleteJob(78);
    }

    function release(){
        $queue = Queue::getInstance();
        $job = new UpdateJob(6666, 'test..');
        $job->setId(79);
        $queue->release('mid', $job);
    }

    function pushExcetionJob(){
        $job = new ExcetionJob();

        $queue = Queue::getInstance();
        $result = $queue->push('high', $job);
        var_dump($result);

    }

}