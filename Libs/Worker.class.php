<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Think\Log;

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
        $work_result = true; // 任务执行结果，默认是成功
        try {
            $this->onJobStart($job);

            $job->beforeHandle();
            $job->handle();

            $this->onJobSuccess($job);
        } catch (\Exception $e) {
            $work_result = false;
            $this->onJobFaild($job);

            $this->handleException($job);
            $job->onError();
        } finally {
            $this->onJobFinish($job, $work_result);
            $job->afterHandle();
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
        $this->onJobFaild($job);
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
    protected function onJobStart(Job $job) {
        $this->manager->startJob($job);
    }

    /**
     * 任务完成后回调
     *
     * @param Job  $job
     * @param bool $work_result
     */
    protected function onJobFinish(Job $job, $work_result = true) {
        $this->manager->endJob($job);

        //失败时考虑重试
        if (!$work_result) {
            if ($job->getAttempts() < $this->options->getMaxRetry()) {
                $this->onJobRelease($job);
            }
        }
    }

    /**
     * 任务完成后回调
     *
     * @param Job $job
     */
    protected function onJobSuccess(Job $job) {
        $this->manager->successJob($job);
    }

    /**
     * 任务完成后回调
     *
     * @param Job $job
     */
    protected function onJobFaild(Job $job) {
        $this->manager->faildJob($job);
    }

    /**
     * 任务完成后回调
     *
     * @param Job $job
     */
    protected function onJobRelease(Job $job) {
        $this->manager->release($job);
    }


}