<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Job;

use Queue\Libs\Job;

class UpdateJob extends Job {

    public $userid;
    public $username;

    /**
     * UpdateJob constructor.
     *
     * @param $userid
     * @param $username
     */
    public function __construct($userid, $username) {
        $this->userid = $userid;
        $this->username = $username;
    }


    public function handle() {
        echo 'update user: ' . $this->userid . ' \r\n';
    }
}