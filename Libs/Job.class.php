<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Queue\Libs\Traits\SerializesObjectTrait;

/**
 * 任务
 */
abstract class Job {

    use SerializesObjectTrait;

    /**
     * 执行任务前
     */
    function beforeHandle() { }

    /**
     * 执行任务
     *
     * @return mixed
     */
    abstract function handle();

    /**
     * 执行任务后
     */
    function afterHandle() { }

    /**
     * 任务执行出现异常时的回调
     */
    function onError() { }

}