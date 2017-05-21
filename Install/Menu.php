<?php

return array(
    array(
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 0,
        //地址，[模块/]控制器/方法
        "route" => "Wechat/Wechat/index",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 0,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status" => 1,
        //名称
        "name" => "队列",
        //备注
        "remark" => "微信相关操作",
        //子菜单列表
        "child" => array(
            array(
                "route" => "Queue/Index/main",
                "type" => 1,
                "status" => 1,
                "name" => "任务列表",
                "remark" => "任务列表"
            ),
            array(
                "route" => "Queue/Job/lists",
                "type" => 0,
                "status" => 1,
                "name" => "获取工作任务列表接口",
            ),
            array(
                "route" => "Queue/Index/repush",
                "type" => 0,
                "status" => 1,
                "name" => "任务重新入列的设置页面",
            ),
            array(
                "route" => "Queue/Job/repush",
                "type" => 0,
                "status" => 1,
                "name" => "任务重新入列操作",
            ),
        ),
    ),
);
