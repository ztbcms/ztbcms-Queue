<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Model;

use Common\Model\Model;

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
     * 运行结果：正常
     */
    const RESULT_SUCCESS = 1;
    /**
     * 运行状态：异常
     */
    const RESULT_ERROR = 2;

}