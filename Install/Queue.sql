CREATE TABLE `cms_queue_job` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL COMMENT '任务类名',
  `queue` varchar(255) NOT NULL DEFAULT '' COMMENT '队列名称',
  `payload` text NOT NULL COMMENT '数据',
  `attempts` tinyint(3) NOT NULL DEFAULT '0' COMMENT '重试次数',
  `reserved_at` bigint(13) unsigned NOT NULL COMMENT '取出时间',
  `available_at` bigint(13) unsigned NOT NULL COMMENT '可用时间',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '任务状态：0排队中,1工作中,2已完成',
  `start_time` bigint(13) unsigned NOT NULL COMMENT '任务执行开始时间',
  `end_time` bigint(13) unsigned NOT NULL COMMENT '任务执行结束时间',
  `result` tinyint(2) NOT NULL DEFAULT '0' COMMENT '执行结果:0无,1正常 2异常',
  `create_time` bigint(13) unsigned NOT NULL COMMENT '任务创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队列任务';