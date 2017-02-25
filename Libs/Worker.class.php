<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

/**
 * Job Worker
 */
class Worker {

    /**
     * @var Queue
     */
    protected $manager;
    /**
     * @var WorkerOptions
     */
    protected $options;

    /**
     * Worker constructor.
     *
     * @param Queue         $queue
     * @param WorkerOptions $options
     */
    public function __construct(Queue $queue, WorkerOptions $options) {
        $this->manager = $queue;
        $this->options = $options;
    }

    /**
     * 执行任务
     *
     * @param string $queue
     */
    public function run($queue = '') {

        $this->beforeRun();

        while (true) {
            $job = $this->getNextJob($queue);
            if ($job) {
                $this->runJob($job);
            } else {
                $this->sleep($this->options->getSleep());
            }

            //
            if ($this->stopIfNecessary()) {
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
     * @return bool
     */
    private function stopIfNecessary() {
        $stop_signal = cache('queue_work_stop');
        if ($stop_signal == Queue::SIGNAL_STOP) {
            return true;
        }

        return false;
    }

    /**
     * 执行Job
     *
     * @param Job $job
     */
    private function runJob(Job $job) {
        try {
            $job->handle();
            $this->onJobFinish($job);
        } catch (\Exception $e) {
            $this->handleException($job->getQueue(), $job);
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
     * @param string $queue
     * @param Job    $job
     */
    protected function handleException($queue = '', Job $job) {
        if ($job->getAttempts() < $this->options->getMaxRetry()) {
            $this->manager->release($queue, $job);
        } else {
            $this->manager->markAs($job, Job::STATUS_ERROR);
        }

    }

    /**
     * worker 工作前
     */
    private function beforeRun() {
        //清除重启
        cache('queue_work_stop', null);
    }

    /**
     * 任务完成后回调
     *
     * @param Job $job
     */
    private function onJobFinish(Job $job) {
        $this->manager->markAs($job, Job::STATUS_FINISH);
        $this->manager->deleteJob($job->getId());
    }

}