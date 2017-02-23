DROP TABLE IF EXISTS `cms_queue_job`;
CREATE TABLE `cms_queue_job` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(64) NOT NULL COMMENT '任务类名',
  `queue` varchar(255) NOT NULL DEFAULT '' COMMENT '队列名称',
  `payload` text NOT NULL COMMENT '数据',
  `attempts` tinyint(3) NOT NULL DEFAULT '0' COMMENT '重试次数',
  `reserved_at` int(11) NOT NULL COMMENT '取出时间',
  `available_at` int(11) NOT NULL COMMENT '可用时间',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '任务状态：0排队中,1工作中,2已完成,3异常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队列任务';