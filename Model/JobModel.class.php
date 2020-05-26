<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Model;

use Common\Model\Model;
use Queue\Libs\Job;

/**
 * 任务模型
 */
class JobModel extends Model {

    protected $tableName = 'queue_job';

    /**
     * 运行状态：排队中
     */
    const STATUS_WAITTING = 0;
    /**
     * 运行状态：工作中
     */
    const STATUS_WORKING = 1;
    /**
     * 运行状态：已完成
     */
    const STATUS_FINISH = 2;
    /**
     * 运行结果：无
     */
    const RESULT_NO = 0;
    /**
     * 运行结果：正常
     */
    const RESULT_SUCCESS = 1;
    /**
     * 运行状态：异常
     */
    const RESULT_ERROR = 2;

    /**
     * 任务ID
     *
     * @var
     */
    protected $id;
    protected $queue;
    protected $payload;
    protected $attempts;
    protected $reserved_at;
    protected $available_at;
    protected $status;
    protected $start_time;
    protected $end_time;
    protected $result;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getQueue() {
        return $this->queue;
    }

    /**
     * @param mixed $queue
     */
    public function setQueue($queue) {
        $this->queue = $queue;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload) {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getAttempts() {
        return $this->attempts;
    }

    /**
     * @param mixed $attempts
     */
    public function setAttempts($attempts) {
        $this->attempts = $attempts;
    }

    /**
     * @return mixed
     */
    public function getReservedAt() {
        return $this->reserved_at;
    }

    /**
     * @param mixed $reserved_at
     */
    public function setReservedAt($reserved_at) {
        $this->reserved_at = $reserved_at;
    }

    /**
     * @return mixed
     */
    public function getAvailableAt() {
        return $this->available_at;
    }

    /**
     * @param mixed $available_at
     */
    public function setAvailableAt($available_at) {
        $this->available_at = $available_at;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStartTime() {
        return $this->start_time;
    }

    /**
     * @param mixed $start_time
     */
    public function setStartTime($start_time) {
        $this->start_time = $start_time;
    }

    /**
     * @return mixed
     */
    public function getEndTime() {
        return $this->end_time;
    }

    /**
     * @param mixed $end_time
     */
    public function setEndTime($end_time) {
        $this->end_time = $end_time;
    }

    /**
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result) {
        $this->result = $result;
    }

    /**
     * @return object|Job
     * @throws \ReflectionException
     */
    function getExcuteJob(){
        $payload = $this->getPayload();
        if(is_string($payload)){
            $payload = json_decode($payload, true);
        }
        $r = new \ReflectionClass($payload['name']);
        $job = $r->newInstanceWithoutConstructor();
        $job->toUnserialize($payload['data']);
        return $job;
    }
}