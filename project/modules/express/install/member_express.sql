DROP TABLE IF EXISTS `phpcms_member_express`;
CREATE TABLE `phpcms_member_express` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL,
  `storeid` int(11) unsigned NOT NULL,
  `expressno` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `company` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `detail` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `createtime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(5) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(5) unsigned NOT NULL DEFAULT '0',
  `pay_money` float NOT NULL DEFAULT '0',
  `price` float unsigned NOT NULL,
  `rebate` tinyint(3) NOT NULL DEFAULT '0',
  `service` bit(8) NOT NULL DEFAULT b'0',
  `user_remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `admin_remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `send_no` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `send_company` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `weight` float unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
