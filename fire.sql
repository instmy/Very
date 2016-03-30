SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `active`;
CREATE TABLE `active` (
  `content` varchar(255) CHARACTER SET gbk DEFAULT NULL,
  `username` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=sjis;

DROP TABLE IF EXISTS `commodity`;
CREATE TABLE `commodity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `introduction` varchar(255) CHARACTER SET gbk DEFAULT NULL,
  `time` int(10) unsigned DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  `transfer` bigint(20) unsigned DEFAULT NULL,
  `region` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `commodity` VALUES ('1', '初窥', '入门套餐，包括 15 GByte 流量', '7776000', '20', '15', '1');

DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount` (
  `card` varchar(64) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `create_time` datetime NOT NULL,
  `used_member` varchar(32) DEFAULT '',
  `discount_price` int(10) unsigned NOT NULL,
  `min_price` int(10) unsigned DEFAULT NULL,
  `username` varchar(32) DEFAULT '',
  PRIMARY KEY (`card`),
  UNIQUE KEY `md5` (`md5`),
  UNIQUE KEY `card` (`card`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `email` varchar(64) CHARACTER SET gbk DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET gbk DEFAULT NULL,
  `password` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `salt` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `money` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `node`;
CREATE TABLE `node` (
  `name` varchar(64) NOT NULL,
  `type` varchar(32) DEFAULT NULL,
  `introduction` varchar(255) DEFAULT NULL,
  `region` int(10) unsigned NOT NULL,
  `address` varchar(64) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `node` VALUES ('节点 A', 'RC4-MD5', '节点 A 的介绍', '1', '1.1.1.1', '节点 A 的备注');

DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `introduction` varchar(255) CHARACTER SET gbk DEFAULT NULL,
  `max_member` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `region` VALUES ('1', 'Fire', 'Fire 是私有的 Shadowsocks 服务', '50');
INSERT INTO `region` VALUES ('2', 'Very', 'Very 区域是第二个基本的区域，目前处于测试阶段，暂不开放销售', '10');

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `token` varchar(64) CHARACTER SET gbk NOT NULL,
  `username` varchar(32) CHARACTER SET gbk DEFAULT NULL,
  `expired_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `token` (`token`,`expired_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;