<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\AdminBase;

class IndexController extends AdminBase {

    /**
     * 队列总览页面
     */
    function main() {
        $this->display();
    }

    /**
     * 任务重新入列的设置页面
     */
    function repush() {
        $job_id = I('get.job_id');
        $job = D('Queue/Job')->where(['id' => $job_id])->find();
        $this->assign('job', $job);
        $this->display();
    }

}