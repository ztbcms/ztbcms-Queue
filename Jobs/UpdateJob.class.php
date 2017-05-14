<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Jobs;

use Queue\Libs\Job;
use Think\Log;

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

    function beforeHandle() {
        Log::write(__CLASS__ . ':' . 'beforeHandle');
    }

    public function handle() {
        Log::write(__CLASS__ . ':' . 'handle');
        echo 'update user: ' . $this->userid . ' \r\n';
    }

    function afterHandle() {
        Log::write(__CLASS__ . ':' . 'afterHandle');
    }

    function onError() {
        Log::write(__CLASS__ . ':' . 'onError');
    }
}