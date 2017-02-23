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

        $this->beforeRun();

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
            $this->onJobFinish($job);
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
     * 处理异常
     *
     * @param Job $job
     */
    protected function handleException(Job $job) {
        $this->manager->markAs($job, Job::STATUS_ERROR);
    }

    /**
     * worker 工作前
     */
    private function beforeRun() { }

    /**
     * 任务完成后回调
     *
     * @param Job $job
     */
    private function onJobFinish(Job $job) {
        $this->manager->markAs($job, Job::STATUS_FINISH);
    }

}