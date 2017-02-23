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
     * 运行状态：异常
     */
    const STATUS_ERROR = 3;

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
     * 执行任务
     *
     * @return mixed
     */
    abstract function handle();

}