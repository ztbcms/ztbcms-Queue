<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\AdminBase;
use Libs\Service\ContentService;
use Queue\Libs\Utils;
use Queue\Model\JobModel;

class ManageController extends AdminBase {

    /**
     * 队列总览页面
     */
    function main() {
        $this->display();
    }

    /**
     * 工作任务列表
     */
    function getJobList() {
        $filter = I('get._filter');
        $operator = I('get._operator');
        $value = I('get._value');
        $page = I('get.page');
        $limit = I('get.limit');
        $data = ContentService::lists('QueueJob', $filter, $operator, $value, 'id DESC', $page, $limit)['data'];
        $this->ajaxReturn(self::createReturn(true, $data));
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

    /**
     * 任务重新入列操作
     */
    function doRepush() {
        $job_id = I('post.job_id');
        $delay = I('post.delay', 0);
        $now = Utils::now();

        $db = D('Queue/Job');
        $job = $db->where(['id' => $job_id])->find();
        if(!$job){
            $this->ajaxReturn(self::createReturn(false, null, '找不到该任务'));
            return;
        }
        unset($job['id']);
        $job['create_time'] = $now;
        $job['available_at'] = $now + $delay * 1000;
        $job['reserved_at'] = 0;
        $job['attempts'] = 0;

        $job['start_time'] = 0;
        $job['end_time'] = 0;
        $job['status'] = JobModel::STATUS_WAITTING;
        $job['result'] = JobModel::RESULT_NO;

        $res = $db->add($job);
        if($res){
            $this->ajaxReturn(self::createReturn(true, $res, '操作成功'));
        }else{
            $this->ajaxReturn(self::createReturn(false, null, $db->getDbError()));
        }
    }

    /**
     * 删除任务
     */
    function doDeleteJob(){
        $job_id = I('post.job_id');
        $db = D('Queue/Job');
        $job = $db->where(['id' => $job_id])->find();
        if(!$job){
            $this->ajaxReturn(self::createReturn(false, null, '找不到该任务'));
            return;
        }
        $db->where(['id' => $job_id])->delete();
        $this->ajaxReturn(self::createReturn(true, null, '操作成功'));
    }

}