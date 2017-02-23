<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Think\Exception;

/**
 * Job Worker
 */
class Worker {

    /**
     * @var Queue
     */
    protected $manager;

    /**
     * Worker constructor.
     *
     * @param Queue $queue
     */
    public function __construct(Queue $queue) {
        $this->manager = $queue;
    }

    /**
     * 执行任务
     *
     * @param string        $queue
     * @param WorkerOptions $options
     */
    public function run($queue = '', WorkerOptions $options) {

        while (true) {
            $job = $this->getNextJob($queue);
            if ($job) {
                $this->runJob($job, $options);
            } else {
                $this->sleep($options->sleep);
            }

            //
            if ($this->stopIfNecessary($options)) {
                break;
            }
        }
    }

    /**
     * 获得下一个Job
     *
     * @param string $queue
     * @return Job|null
     */
    protected function getNextJob($queue = '') {
        foreach (explode(',', $queue) as $q) {
            $job = $this->manager->pop($q);
            if (!is_null($job)) {
                return $job;
            }
        }

        return null;
    }

    /**
     * 检测是否需要停止队列
     *
     * @param WorkerOptions $options
     * @return bool
     */
    private function stopIfNecessary($options) {
        return false;
    }

    /**
     * 执行Job
     *
     * @param Job           $job
     * @param WorkerOptions $options
     */
    private function runJob($job, $options) {
        try {
            $job->handle();
        } catch (\Exception $e) {
            $this->handleException($job);
        }
    }

    /**
     * 无任务时休眠
     *
     * @param int $sleep
     */
    private function sleep($sleep) {
        sleep($sleep);
    }

    /**
     * 标识任务状态
     *
     * @param Job $job
     * @param int $status
     */
    protected function markAs($job, $status) {
        $db = D('Queue\Job');
        $db->where(['id' => $job->getId()])->save(['status' => $status]);
    }

    /**
     * 处理异常
     *
     * @param Job $job
     */
    protected function handleException(Job $job) {
        $this->markAs($job, Job::STATUS_ERROR);
    }

}