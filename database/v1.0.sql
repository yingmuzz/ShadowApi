DROP TABLE IF EXISTS `shadow_users`;
CREATE TABLE `shadow_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `account` varchar(64) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(512) NOT NULL DEFAULT '' COMMENT '密码',
  `google_auth` varchar(256) NOT NULL DEFAULT '' COMMENT 'Google授权码',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '状态:0-已禁用;1-使用中',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `account` (`account`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT '后台管理员信息表';
