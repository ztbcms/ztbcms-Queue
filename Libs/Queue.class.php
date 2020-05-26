<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Queue\Libs\Queues\DatabaseQueue;

abstract class Queue {

    /**
     * 停止的信息号
     */
    const SIGNAL_STOP = 1;

    /**
     * @var Queue
     */
    private static $queue;

    /**
     * @return Queue
     */
    static function getInstance() {
        if (empty(static::$queue)) {
            //目前默认是DB
            return static::$queue = new DatabaseQueue();
        }

        return static::$queue;
    }

    /**
     * 添加
     *
     * @param string $queue 队列名称
     * @param Job    $job   工作任务
     * @param int    $delay 延迟时长，单位：秒
     * @return Job
     */
    abstract function push($queue, Job $job, $delay = 0);

    /**
     * 取出
     *
     * @param string $queue
     * @return Job|null
     */
    abstract function pop($queue = '');

    /**
     * 获取该任务的属性数据
     *
     * @param Job $job
     * @return array
     */
    function createPayload($job) {
        $properties = get_class_vars(get_class($job));
        $ret = array();
        foreach ($properties as $key => $val) {
            $ret[$key] = $job->$key;
        }

        return $ret;
    }

    /**
     * @param string $job_id
     * @param string $name
     * @param string $queue
     * @param string $playload
     * @param int    $attempts
     * @param int    $reserved_at
     * @param int    $available_at
     * @param string $status
     * @internal param $data
     * @return Job
     */
    function asJob(
        $job_id,
        $name,
        $queue = '',
        $playload = '',
        $attempts = 0,
        $reserved_at = 0,
        $available_at = 0,
        $status
    ) {

        $playload = json_decode($playload, true);

        $job = $this->instanceJob($name);
        $job->setAttempts($attempts);
        $job->setAvailableAt($available_at);
        $job->setId($job_id);
        $job->setPlayload($playload);
        $job->setQueue($queue);
        $job->setReservedAt($reserved_at);
        $job->setStatus($status);

        //properties
        $properties = get_class_vars(get_class($job));
        foreach ($properties as $key => $val) {
            $job->$key = $playload[$key];
        }

        return $job;
    }

    /**
     *实例化Job类
     *
     * @param $name
     *
     * @return object|Job
     */
    protected function instanceJob($name) {
        $r1 = new \ReflectionClass($name);
        return $r1->newInstanceWithoutConstructor();
    }

    /**
     * 标识任务状态
     *
     * @param Job $job
     * @param int $status
     */
    abstract function markAs($job, $status);

    /**
     * 任务工作开始时
     *
     * @param Job $job
     * @return mixed
     */
    abstract function startJob(Job $job);

    /**
     * 任务工作结束时
     *
     * @param Job $job
     * @return mixed
     */
    abstract function endJob(Job $job);

    /**
     * 任务工作异常时
     *
     * @param Job $job
     * @return mixed
     */
    abstract function faildJob(Job $job);

    /**
     * 任务成功执行完成时
     *
     * @param Job $job
     * @return mixed
     */
    abstract function successJob(Job $job);

    /**
     * 删除任务
     *
     * @param $id
     * @return mixed
     */
    abstract function deleteJob($id);

    /**
     * 把Job重新
     *
     * @param Job    $job
     * @return mixed
     */
    abstract function release(Job $job);

}