<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Queue\Libs;

class Utils {

    /**
     * 获取毫秒级别的时间戳
     *
     * @return float
     */
    static function now() {

        return round(microtime(true) * 1000);

    }

}