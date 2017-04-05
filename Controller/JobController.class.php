<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\AdminBase;
use Libs\Service\ContentService;

/**
 * Job相关接口
 */
class JobController extends AdminBase {

    /**
     * 工作任务列表
     */
    function lists() {
        $filter = I('get._filter');
        $operator = I('get._operator');
        $value = I('get._value');
        $page = I('get.page');
        $limit = I('get.limit');
        $data = ContentService::lists('QueueJob', $filter, $operator, $value,'id DESC', $page, $limit)['data'];
        $this->ajaxReturn(self::createReturn(true, $data));
    }

}