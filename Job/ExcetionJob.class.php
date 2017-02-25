<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Job;

use Queue\Libs\Job;

/**
 * 异常任务
 */
class ExcetionJob extends Job {

    /**
     * 执行任务
     *
     * @return mixed
     * @throws \Exception
     */
    function handle() {
        echo 'work with a excetion..\r';
        throw new \Exception('test excetion...');
    }
}