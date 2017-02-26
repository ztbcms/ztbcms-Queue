<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\Base;

class QueueController extends Base {


    protected function _initialize() {
        parent::_initialize();
        //私钥校验
        $queue_secret_key = I('get._qsk', '');
        if ($queue_secret_key != C('QUEUE_SECRET_KEY')) {
            echo '私钥不匹配';
            exit();
        }
    }


}