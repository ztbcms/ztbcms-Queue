<?php
/**
 * User: jayinton
 * Date: 2020/5/26
 * Time: 16:05
 */

namespace Queue\Model;


use Think\Model;

//失败任务
class FaildJobModel extends Model
{
    protected $tableName = 'queue_faild_job';
}