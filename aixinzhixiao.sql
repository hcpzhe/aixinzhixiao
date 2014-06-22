/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : aixinzhixiao

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-06-22 22:37:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ax_levelup`
-- ----------------------------
DROP TABLE IF EXISTS `ax_levelup`;
CREATE TABLE `ax_levelup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '申请用户ID',
  `apply_tine` varchar(20) NOT NULL DEFAULT '0' COMMENT '申请时间 unix',
  `level_bef` tinyint(1) unsigned NOT NULL COMMENT '升级前级别',
  `level_aft` tinyint(1) unsigned NOT NULL COMMENT '升级后级别',
  `should_pay` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `real_pay` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际打款金额',
  `pay_time` varchar(255) DEFAULT NULL COMMENT '打款时间string',
  `rec_id` int(10) unsigned NOT NULL COMMENT '收益用户ID',
  `verify_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '审核时间 unix',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1-待审核, 2-拒绝, 3-通过',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '升级类型 1-付款升级 2-积分升级 3-人工升级',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注留言',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='升级记录';

-- ----------------------------
-- Records of ax_levelup
-- ----------------------------

-- ----------------------------
-- Table structure for `ax_member`
-- ----------------------------
DROP TABLE IF EXISTS `ax_member`;
CREATE TABLE `ax_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(40) NOT NULL,
  `password` char(32) NOT NULL COMMENT '密码',
  `pwd_two` char(32) NOT NULL COMMENT '二级密码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ax_member
-- ----------------------------

-- ----------------------------
-- Table structure for `ax_user`
-- ----------------------------
DROP TABLE IF EXISTS `ax_user`;
CREATE TABLE `ax_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(20) NOT NULL COMMENT '帐号',
  `password` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ax_user
-- ----------------------------
