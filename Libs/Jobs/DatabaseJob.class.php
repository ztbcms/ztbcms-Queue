<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs\Jobs;


use Queue\Libs\Job;

class DatabaseJob extends Job {


    /**
     * 执行任务
     *
     * @return mixed
     */
    function handle() {
        // TODO: Implement handle() method.
        echo 'it works!';
    }
}