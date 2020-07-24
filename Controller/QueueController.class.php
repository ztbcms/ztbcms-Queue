<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Controller;

use Common\Controller\Base;

class QueueController extends Base {


    protected function _initialize() {
        parent::_initialize();

        if (!IS_CLI) {
            echo '请用命令行运行！';
            exit;
        }
    }


}