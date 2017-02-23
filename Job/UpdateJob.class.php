<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Job;

use Queue\Libs\Job;

class UpdateJob extends Job {

    public $userid;
    public $username;

    public function handle() {
        echo 'update user: ' . $this->userid . ' \r\n';
    }
}