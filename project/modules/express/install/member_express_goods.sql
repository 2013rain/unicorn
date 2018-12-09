DROP TABLE IF EXISTS `phpcms_member_express_goods`;
CREATE TABLE `phpcms_member_express_goods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL,
  `expressno` varchar(255) NOT NULL DEFAULT '',
  `goodsname` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `pcategory` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `scategory` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `productname` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `goodsmodel` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `uprice` float unsigned NOT NULL,
  `num` int(11) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
