<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

abstract class Job {

    abstract function handle();

    abstract function _createJob($queue = '');

//    abstract function _loadJob();

}