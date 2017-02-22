<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Job;

use Queue\Libs\Jobs\DatabaseJob;

class UpdateJob extends DatabaseJob {

    public $userid;
    public $username;

    public function handle() {
        // TODO: Implement handle() method.
    }
}