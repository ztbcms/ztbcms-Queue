<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\Base;
use Queue\Job\UpdateJob;

class TestController extends Base {

    function index() {

        $job = new UpdateJob();
        $job->userid = '111';
        $job->username = 'jayin';
        $data = $job->_createJob();
        var_dump($data);
//        var_dump(get_class_vars('Queue\Job\UpdateJob'));
    }

}