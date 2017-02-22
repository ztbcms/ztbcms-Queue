<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs\Jobs;


use Queue\Libs\Job;

class DatabaseJob extends Job {


    function handle() {
        // TODO: Implement handle() method.
    }

    function _createJob($queue = '') {
        $data = get_class_vars(get_class($this));

        return [
            'queue' => $queue,
            'payload' => json_encode($data),
            'attempts' => 0,
            'available_at' => time(),
            'reserved_at' => 0,
        ];
    }
}