SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `passwd` varchar(16) NOT NULL,
  `transfer_enable` bigint(20) NOT NULL,
  `port` int(11) NOT NULL,
  `enable` tinyint(4) DEFAULT '1',
  `u` bigint(10) unsigned DEFAULT '0',
  `d` bigint(10) unsigned DEFAULT '0',
  `switch` int(10) unsigned DEFAULT '1',
  `t` int(10) unsigned DEFAULT '0',
  `expired_time` int(10) unsigned DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`,`port`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `user` VALUES ('1', 'your_password', '0', '10001', '1', '0', '0', '1', '0', '0', 'fire');