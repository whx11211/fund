# Host: localhost  (Version: 5.5.53)
# Date: 2018-05-25 11:28:14
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "fund_info"
#

DROP TABLE IF EXISTS `fund_info`;
CREATE TABLE `fund_info` (
  `code` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '基金代码',
  `company` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '基金公司',
  `type` varchar(64) COLLATE utf8_bin NOT NULL COMMENT '基金类型',
  `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '基金全称',
  `free_rate` decimal(7,4) NOT NULL DEFAULT '0.001' COMMENT '买入费率',
  `administrator` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '基金管理人',
  `manager` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '基金经理',
  PRIMARY KEY (`code`)
) /*!50100 STORAGE DISK */ ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

#
# Data for table "fund_info"
#

INSERT INTO `fund_info` (`code`,`company`,`type`,`name`,`administrator`,`manager`) VALUES ('000961','tianhong','指数型','天弘沪深300指数A',' 天弘基金管理有限公司','张子法 陈瑶女士'),('000962','tianhong','指数型','天弘中证500A',' 天弘基金管理有限公司','张子法 陈瑶女士'),('001542','guotai','股票型','国泰互联网+股票','中国建设银行股份有限公司','彭凌志 '),('001548','tianhong','指数型','上证50A',' 天弘基金管理有限公司','张子法 陈瑶女士'),('001552','tianhong','指数型','证券保险A',' 天弘基金管理有限公司','张子法 陈瑶女士'),('001592','tianhong','指数型','创业板A',' 天弘基金管理有限公司','张子法 陈瑶女士'),('001631','tianhong','指数型','食品饮料A',' 天弘基金管理有限公司','张子法 陈瑶女士');

#
# Structure for table "fund_net_unit"
#

DROP TABLE IF EXISTS `fund_net_unit`;
CREATE TABLE `fund_net_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00' COMMENT '时间',
  `unit_value` decimal(7,4) NOT NULL DEFAULT '0.0000' COMMENT '单位净值',
  `unit_change` decimal(7,4) NOT NULL DEFAULT '0.0000' COMMENT '净值变化',
  `trend` tinyint(1) NOT NULL DEFAULT 0 COMMENT '曲线走势 -1下降 0震荡 1上升',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tm_idx` (`date`)
) /*!50100 STORAGE DISK */ ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

#
# Data for table "fund_net_unit"
#

