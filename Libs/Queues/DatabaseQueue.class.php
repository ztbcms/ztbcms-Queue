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
     * @param string $queue
     * @param Job    $job
     * @param int    $delay 延迟时间，单位：秒
     * @return mixed
     */
    function push($queue, Job $job, $delay = 0) {
        $now = Utils::now();
        $job_data = [
            'name' => get_class($job),
            'queue' => $queue,
            'payload' => json_encode(static::createPlayload($job)),
            'attempts' => 0,
            'available_at' => $now + $delay * 1000,
            'reserved_at' => 0,
            'create_time' => $now
        ];

        return $this->db->add($job_data);
    }

    /**
     * 取出
     *
     * @param string $queue
     * @return Job|null
     */
    function pop($queue = '') {
        $this->db->startTrans();
        $now = Utils::now();
        $job_record = $this->db->where([
            'status' => JobModel::STATUS_WAITTING,
            'queue' => $queue,
            'available_at' => ['ELT', $now],
            'reserved_at' => 0, //未取出的
        ])->order('available_at ASC, id ASC')->lock(true)->find();
        $this->db->commit();

        if (empty($job_record)) {
            return null;
        }

        $job = $this->asJob($job_record['id'], $job_record['name'], $job_record['queue'], $job_record['payload'],
            $job_record['attempts'], $job_record['reserved_at'], $job_record['available_at'], $job_record['status']);

        //标志
        $this->updateJob($job->getId(), ['reserved_at' => $now]);

        $this->db->commit();

        return $job;
    }


    /**
     * 标识任务状态
     *
     * @param Job $job
     * @param int $status
     */
    public function markAs($job, $status) {
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
     * @param Job    $job
     * @return mixed
     */
    public function release(Job $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->lock(true)->save([
            'reserved_at' => 0, //清空取出时间
            'attempts' => $job->getAttempts() + 1,
            'status' => JobModel::STATUS_WAITTING
        ]);
        $this->db->commit();

    }

    /**
     * 任务工作开始时
     *
     * @param Job $job
     * @return mixed
     */
    function startJob(Job $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['start_time' => Utils::now(), 'status' => JobModel::STATUS_WORKING]);
        $this->db->commit();
    }

    /**
     * 任务工作结束时
     *
     * @param Job $job
     * @return mixed
     */
    function endJob(Job $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['end_time' => Utils::now(), 'status' => JobModel::STATUS_FINISH]);
        $this->db->commit();
    }

    /**
     * 任务工作异常时
     *
     * @param Job $job
     * @return mixed
     */
    function faildJob(Job $job) {
        Log::write('faildJob!!');
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['result' => JobModel::RESULT_ERROR]);
        $this->db->commit();
    }

    /**
     * 任务成功执行完成时
     *
     * @param Job $job
     * @return mixed
     */
    function successJob(Job $job) {
        Log::write('successJob!!');
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->save(['result' => JobModel::RESULT_SUCCESS]);
        $this->db->commit();
    }
}