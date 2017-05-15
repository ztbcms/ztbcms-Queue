<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Jobs;

use Queue\Libs\Job;

class HugeJob extends Job {

    /**
     * 执行任务
     *
     * @return mixed
     */
    function handle() {
        sleep(2);
        echo 'ID: ' . $this->getId() . ' work with a Huge Job..\\r\\n';
    }
}