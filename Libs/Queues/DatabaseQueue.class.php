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
     * @return mixed
     */
    function push($queue = '', Job $job) {
        $job_data = [
            'name' => get_class($job),
            'queue' => $queue,
            'payload' => json_encode(static::createPlayload($job)),
            'attempts' => 0,
            'available_at' => time(),
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
            'queue' => $queue
        ])->order('id ASC')->lock(true)->find();

        if (empty($job_record)) {
            return null;
        }

        $job = $this->asJob($job_record['id'], $job_record['name'], $job_record['queue'], $job_record['payload'],
            $job_record['attempts'], $job_record['reserved_at'], $job_record['available_at'], $job_record['status']);

        //标志
        $this->markAs($job, Job::STATUS_WORKING);

        $this->db->commit();

        return $job;
    }

    /**
     * @param Job $job
     * @param int $status
     */
    private function markAs($job, $status) {
        $this->db->where(['id' => $job->getId()])->save(['status' => $status]);
    }

}