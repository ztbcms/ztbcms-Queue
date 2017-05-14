<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Jobs;

use Queue\Libs\Job;
use Think\Log;

/**
 * 异常任务
 */
class ExcetionJob extends Job {


    function beforeHandle() {
        Log::write(__CLASS__ . ':' . 'beforeHandle');
    }

    /**
     * 执行任务
     *
     * @return mixed
     * @throws \Exception
     */
    function handle() {
        Log::write(__CLASS__ . ':' . 'handle');
        echo 'work with a excetion..\r';
        throw new \Exception('test excetion...');
    }


    function afterHandle() {
        Log::write(__CLASS__ . ':' . 'afterHandle');
    }

    function onError() {
        Log::write(__CLASS__ . ':' . 'onError');
    }
}