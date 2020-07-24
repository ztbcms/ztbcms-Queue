<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

use Queue\Model\JobModel;
use Think\Exception;
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
                Utils::log("Exit queue.");
                break;
            }
        }
    }

    /**
     * 获得下一个Job
     *
     * @param string $queue
     * @return JobModel|null
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
     * @param JobModel $jobObject
     */
    private function runJob(JobModel $jobObject) {
        $work_result = true; // 任务执行结果，默认是成功
        Utils::log("Processing [" . $jobObject->getName().'] JobId:'.$jobObject->getId());
        $excuteJob = $jobObject->getExcuteJob();
        try {
            $this->onJobStart($jobObject);

            $excuteJob->beforeHandle();
            $excuteJob->handle();

            $this->onJobSuccess($jobObject);
        } catch (\Throwable $e) {
            $work_result = false;
            $this->onJobFaild($jobObject, $e);

            $excuteJob->onError();
        } finally {
            $excuteJob->afterHandle();
            $this->onJobFinish($jobObject, $work_result);

            Utils::log("Processed " . $jobObject->getName());
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
    protected function onJobStart(JobModel $job) {
        $this->manager->startJob($job);
    }

    /**
     * 任务完成后回调
     *
     * @param  JobModel  $job
     * @param  bool  $work_result
     */
    protected function onJobFinish(JobModel $job, $work_result = true) {
        $this->manager->endJob($job);

        //失败时考虑重试
        if (!$work_result && $job->getAttempts() < $this->options->getMaxRetry()) {
            $this->onJobRelease($job);
        } else {
            //结束后且无法重试，删除任务
            $this->manager->deleteJob($job->getId());
        }
    }

    /**
     * 任务完成后回调
     *
     * @param  JobModel  $job
     */
    protected function onJobSuccess(JobModel $job) {
        $this->manager->successJob($job);
    }

    /**
     * 任务完成后回调
     *
     * @param  JobModel  $job
     * @param  \Throwable  $exception
     */
    protected function onJobFaild(JobModel $job, \Throwable $exception) {
        $this->manager->faildJob($job, $exception);
    }

    /**
     * 任务完成后回调
     *
     * @param  JobModel  $job
     */
    protected function onJobRelease(JobModel $job) {
        $this->manager->release($job);
    }


}