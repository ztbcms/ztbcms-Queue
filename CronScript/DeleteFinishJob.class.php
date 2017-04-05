<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\CronScript;

use Cron\Base\Cron;
use Queue\Libs\Job;

/**
 *  定期删除已完成的工作任务，推荐每日执行一次
 */
class DeleteFinishJob extends Cron {

    /**
     * 执行任务回调
     *
     * @param string $cronId
     */
    public function run($cronId) {
        $hour = 7 * 24; //删除X小时前已完成的任务

        $limit_time = time() - $hour * 60 * 60;
        $db = D('Queue/Job');
        $db->where(['status' => Job::STATUS_FINISH, 'reserved_at' => ['ELT', $limit_time]])->delete();
    }
}