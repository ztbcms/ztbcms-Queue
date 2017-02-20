DROP TABLE IF EXISTS `cms_cron`;
CREATE TABLE `cms_queue_job` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `queue` varchar(255) NOT NULL DEFAULT '' COMMENT '队列名称',
  `payload` text NOT NULL COMMENT '数据',
  `attempts` tinyint(3) NOT NULL DEFAULT '0' COMMENT '重试次数',
  `reserved_at` int(11) NOT NULL COMMENT '保留时间',
  `available_at` int(11) NOT NULL COMMENT '可用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队列任务';