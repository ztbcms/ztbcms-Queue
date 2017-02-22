<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\Base;
use Queue\Job\UpdateJob;
use Queue\Libs\Queue;

class TestController extends Base {

    function push() {

        $job = new UpdateJob();
        $job->userid = '111';
        $job->username = 'jayin';

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

}