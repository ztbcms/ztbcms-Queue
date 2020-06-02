<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs\Queues;

use Queue\Libs\Job;
use Queue\Libs\Queue;
use Queue\Libs\Utils;
use Queue\Model\JobModel;
use Think\Log;

/**
 * 基于数据库的队列
 */
class DatabaseQueue extends Queue {

    /**
     * @var JobModel
     */
    private $db;

    public function __construct() {
        $this->db = D('Queue/Job');
    }


    /**
     * 添加
     *
     * @param  string  $queue
     * @param  Job  $job
     * @param  int  $delay  延迟时间，单位：秒
     * @return mixed
     * @throws \ReflectionException
     */
    function push($queue, Job $job, $delay = 0) {
        $now = Utils::now();
        $job_data = [
            'name' => get_class($job),
            'queue' => $queue,
            'payload' => json_encode([
                'name' => get_class($job),
                'data' => $this->createPayload($job),
            ]),
            'attempts' => 0,
            'available_at' => $now + $delay * 1000,
            'reserved_at' => 0,
            'create_time' => $now
        ];

        return $this->db->add($job_data);
    }

    /**
     * 获取该任务的属性数据
     *
     * @param  Job  $job
     * @return array
     * @throws \ReflectionException
     */
    function createPayload(Job $job) {
        return  $job->toSerialize();
    }

    /**
     * 取出
     *
     * @param string $queue
     * @return JobModel|null
     */
    function pop($queue = '') {
        $this->db->startTrans();
        $now = Utils::now();
        $job_record = $this->db->where([
            'queue' => $queue,
            'available_at' => ['ELT', $now],
            'reserved_at' => 0, //未取出的
        ])->order('available_at ASC, id ASC')->find();

        if (empty($job_record)) {
            return null;
        }

        //标志取出
        $res = $this->db->where(['id' => $job_record['id'], 'reserved_at' => 0])->save( ['reserved_at' => $now]);
        if(!$res){
            return null;
        }
        $this->db->commit();

        $job = new JobModel();
        $job->setId($job_record['id']);
        $job->setName($job_record['name']);
        $job->setAttempts($job_record['attempts']);
        $job->setAvailableAt($job_record['available_at']);
        $job->setEndTime($job_record['end_time']);
        $job->setPayload($job_record['payload']);
        $job->setQueue($job_record['queue']);
        $job->setReservedAt($job_record['reserved_at']);
        $job->setResult($job_record['result']);
        $job->setStartTime($job_record['start_time']);
        $job->setStatus($job_record['status']);
        
        return $job;
    }


    /**
     * 标识任务状态
     *
     * @param JobModel $job
     * @param int $status
     */
    public function markAs(JobModel $job, $status) {
        $this->updateJob($job->getId(), ['status' => $status]);
    }

    /**
     * 更新Job
     *
     * @param string $job_id
     * @param array  $data
     */
    public function updateJob($job_id, array $data) {
        $this->db->startTrans();
        $this->db->where(['id' => $job_id])->save($data);
        $this->db->commit();
    }

    /**
     * 删除任务
     *
     * @param $id
     * @return mixed
     */
    public function deleteJob($id) {
        $this->db->startTrans();
        $this->db->where(['id' => $id])->delete();
        $this->db->commit();
    }

    /**
     * 把Job重新放到待运行队列
     *
     * @param  JobModel  $job
     *
     * @return mixed
     */
    public function release(JobModel $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->lock(true)->save([
            'reserved_at' => 0, //清空取出时间
            'attempts' => $job->getAttempts() + 1,
            'status' => JobModel::STATUS_WAITTING,
            'result' => JobModel::RESULT_NO,
            'start_time' => 0,
            'end_time' => 0,

        ]);
        $this->db->commit();

    }

    /**
     * 任务工作开始时
     *
     * @param  JobModel  $job
     *
     * @return mixed
     */
    function startJob(JobModel $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['start_time' => Utils::now(), 'status' => JobModel::STATUS_WORKING]);
        $this->db->commit();
    }

    /**
     * 任务工作结束时
     *
     * @param  JobModel  $job
     *
     * @return mixed
     */
    function endJob(JobModel $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['end_time' => Utils::now(), 'status' => JobModel::STATUS_FINISH]);
        $this->db->commit();
    }

    /**
     * 任务工作异常时
     *
     * @param  JobModel  $job
     * @param  \Exception  $exception
     *
     * @return mixed
     */
    function faildJob(JobModel $job, \Throwable $exception) {
        $this->db->startTrans();
        $m = $this->db->where(['id' => $job->getId()])->find();
        $this->db->where(['id' => $job->getId()])->save(['result' => JobModel::RESULT_ERROR]);
        // 记录异常任务
        unset($m['id']);
        $m['exception'] = $exception->getMessage() . ' ' . $exception->getTraceAsString();
        D('Queue/FaildJob')->add($m);
        $this->db->commit();
    }

    /**
     * 任务成功执行完成时
     *
     * @param  JobModel  $job
     *
     * @return mixed
     */
    function successJob(JobModel $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['result' => JobModel::RESULT_SUCCESS]);
        $this->db->commit();
    }
}