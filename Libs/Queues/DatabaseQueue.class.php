<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs\Queues;

use Queue\Libs\Job;
use Queue\Libs\Queue;
use Queue\Model\JobModel;

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
     * @param int    $delay
     * @return mixed
     */
    function push($queue = '', Job $job, $delay = 0) {
        $job_data = [
            'name' => get_class($job),
            'queue' => $queue,
            'payload' => json_encode(static::createPlayload($job)),
            'attempts' => 0,
            'available_at' => time() + $delay,
            'reserved_at' => 0,
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
        $job_record = $this->db->where([
            'status' => Job::STATUS_WAITTING,
            'queue' => $queue,
            'available_at' => ['ELT', time()]
        ])->order('available_at ASC, id ASC')->lock(true)->find();
        $this->db->commit();

        if (empty($job_record)) {
            return null;
        }

        $job = $this->asJob($job_record['id'], $job_record['name'], $job_record['queue'], $job_record['payload'],
            $job_record['attempts'], $job_record['reserved_at'], $job_record['available_at'], $job_record['status']);

        //标志
        $this->markAs($job, Job::STATUS_WORKING);
        $this->updateJob($job->getId(), ['reserved_at' => time()]);

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
     * @param array $data
     */
    public function updateJob($job_id, array $data){
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
     * @param string $queue
     * @param Job    $job
     * @return mixed
     */
    public function release($queue = '', Job $job) {
        $this->db->startTrans();
        $this->db->where(['id' => $job->getId()])->lock(true)->save([
            'attempts' => $job->getAttempts() + 1,
            'queue' => $queue,
            'status' => Job::STATUS_WAITTING
        ]);
        $this->db->commit();

    }
}