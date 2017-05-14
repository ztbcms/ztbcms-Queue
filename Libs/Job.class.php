<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

/**
 * 任务
 */
abstract class Job {

    /**
     * 任务ID
     *
     * @var
     */
    protected $id;
    protected $queue;
    protected $playload;
    protected $attempts;
    protected $reserved_at;
    protected $available_at;
    protected $status;
    protected $start_time;
    protected $end_time;
    protected $result;


    /**
     * 执行任务前
     */
    function beforeHandle() { }

    /**
     * 执行任务
     *
     * @return mixed
     */
    abstract function handle();

    /**
     * 执行任务后
     */
    function afterHandle() { }

    /**
     * 任务执行出现异常时的回调
     */
    function onError() { }

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
    public function getPlayload() {
        return $this->playload;
    }

    /**
     * @param mixed $playload
     */
    public function setPlayload($playload) {
        $this->playload = $playload;
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


}