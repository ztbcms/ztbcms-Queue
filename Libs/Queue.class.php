<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Queue\Libs\Queues\DatabaseQueue;
use Queue\Model\JobModel;

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
     * @return JobModel|null
     */
    abstract function pop($queue = '');

    function asJob($payload)
    {
        $payload = json_decode($payload, true);
        $job = $this->instanceJob($payload['name'], $payload['data']);
        return $job;
    }

    /**
     *实例化Job类
     *
     * @param $name
     *
     * @param  array  $data
     *
     * @return object|Job
     * @throws \ReflectionException
     */
    protected function instanceJob($name,array $data = []) {
        $reflectionClass = new \ReflectionClass($name);
        $job = $reflectionClass->newInstanceWithoutConstructor();
        $job->toUnserialize($data);
        return $job;
    }

    /**
     * 标识任务状态
     *
     * @param JobModel $job
     * @param int $status
     */
    abstract function markAs(JobModel $job, $status);

    /**
     * 任务工作开始时
     *
     * @param JobModel $job
     * @return mixed
     */
    abstract function startJob(JobModel $job);

    /**
     * 任务工作结束时
     *
     * @param JobModel $job
     * @return mixed
     */
    abstract function endJob(JobModel $job);

    /**
     * 任务工作异常时
     *
     * @param JobModel $job
     * @return mixed
     */
    abstract function faildJob(JobModel $job, \Exception $exception);

    /**
     * 任务成功执行完成时
     *
     * @param JobModel $job
     * @return mixed
     */
    abstract function successJob(JobModel $job);

    /**
     * 删除任务
     * TODO 改为一个Job对象
     * @param $id
     * @return mixed
     */
    abstract function deleteJob($id);

    /**
     * 把Job重新
     *
     * @param JobModel    $job
     * @return mixed
     */
    abstract function release(JobModel $job);

}