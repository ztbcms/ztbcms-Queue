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

}