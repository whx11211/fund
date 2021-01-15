# Host: localhost  (Version: 5.5.53)
# Date: 2018-05-25 11:28:14
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "fund_info"
#

DROP TABLE IF EXISTS `fund_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `fund_info` (
  `code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '基金代码',
  `company` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '基金公司',
  `type` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '基金类型',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '基金全称',
  `founding_time` date DEFAULT NULL COMMENT '成立时间',
  `free_rate` decimal(7,4) NOT NULL DEFAULT '0.0010' COMMENT '买入费率',
  `administrator` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '基金管理人',
  `manager` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '基金经理',
  `holding` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否持有',
  `param` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '额外参数 拉取基金信息参数',
  PRIMARY KEY (`code`) USING BTREE
) /*!50100 STORAGE DISK */ ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;


#
# Data for table "fund_info"
#

LOCK TABLES `fund_info` WRITE;
/*!40000 ALTER TABLE `fund_info` DISABLE KEYS */;
INSERT INTO `fund_info` VALUES ('000961','tianhong','指数型','天弘沪深300指数A','2015-01-23',0.0010,'天弘基金管理有限公司','张子法 陈瑶女士',1,''),('000962','tianhong','指数型','天弘中证500A','2015-01-23',0.0010,'天弘基金管理有限公司','张子法 陈瑶女士',0,''),('000968','guangfa','指数型','广发中证养老指数A类','2015-02-13',0.0012,'中国银行股份有限公司','刘杰',1,''),('001542','guotai','股票型','国泰互联网+股票','2015-07-21',0.0015,'中国建设银行股份有限公司','彭凌志 ',1,''),('001548','tianhong','指数型','上证50A','2015-07-17',0.0010,'天弘基金管理有限公司','张子法 陈瑶女士',0,''),('001550','tianhong','指数型','天弘中证医药100A','2015-06-30',0.0010,'天弘基金管理有限公司','沙川',1,''),('001592','tianhong','指数型','天弘创业板A','2015-07-10',0.0010,'天弘基金管理有限公司','张子法 陈瑶女士',1,''),('001631','tianhong','指数型','天弘食品饮料A','2015-07-31',0.0010,'天弘基金管理有限公司','张子法 陈瑶女士',0,''),('005726','guotai','混合型','国泰价值精选灵活配置混合','2018-08-10',0.0010,'中国银行股份有限公司','周伟锋',1,''),('007345','fuguo','混合型','富国科技创新灵活配置混合','2019-05-06',0.0015,'中国建设银行股份有限公司','李元博',1,''),('100032','fuguo','指数型','富国中证红利指数增强','2008-11-20',0.0015,'中国工商银行>股份有限公司','徐幼华',1,''),('110011','yifangda','混合型','易方达中小盘混合','2008-06-19',0.0015,'易方达基金管理有限公司','张坤',1,''),('160626','penghua','指数型','鹏华中证信息技术指数分级','2014-05-08',0.0012,'中国建设银行股份有限公司','余斌',1,'557'),('501050','huaxia','指数型','华夏沪港通上证50AH优选','2016-10-28',0.0015,'中国建设银行股份有限公司','荣膺',1,''),('502010','yifangda','指数型','易方达证券公司分级','2015-07-09',0.0010,'易方达基金管理有限公司','余海燕',1,'');

INSERT INTO `fund_info` VALUES ('008764','tianhong','QDII','天弘越南市场股票（QDII）C','2020-01-20',0,'天弘基金管理有限公司','胡超',1,'');
INSERT INTO `fund_info` VALUES ('001594','tianhong','指数型','天弘中证银行指数A','2015-07-08',0.0010,'天弘基金管理有限公司','陈瑶',1,'');
INSERT INTO `fund_info` VALUES ('001550','tianhong','指数型','天弘中证医药100指数A','2015-06-30',0.0010,'天弘基金管理有限公司','沙川',1,'');
INSERT INTO `fund_info` VALUES ('161028','fuguo','指数型','富国中证新能源汽车','2019-05-06',0.0012,'中国建设银行股份有限公司','牛志冬',1,'');
INSERT INTO `fund_info` VALUES ('008086','huaxia','指数型','华夏中证5G通讯主题ETF','2019-12-10',0.0012,'中国建设银行股份有限公司','李俊',1,'');
INSERT INTO `fund_info` VALUES ('110022','yifangda','股票型','易方达消费行业股票','2010-08-20',0.0015,'易方达基金管理有限公司','萧楠',1,'');

/*!40000 ALTER TABLE `fund_info` ENABLE KEYS */;
UNLOCK TABLES;


#
# Structure for table "fund_net_unit"
#

DROP TABLE IF EXISTS `fund_net_unit`;
CREATE TABLE `fund_net_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-01-01' COMMENT '时间',
  `unit_value` decimal(7,4) NOT NULL DEFAULT '0.0000' COMMENT '单位净值',
  `unit_change` decimal(7,4) NOT NULL DEFAULT '0.0000' COMMENT '净值变化',
  `trend` tinyint(1) NOT NULL DEFAULT 0 COMMENT '曲线走势 -1下降 0震荡 1上升',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tm_idx` (`date`)
) /*!50100 STORAGE DISK */ ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

#
# Data for table "fund_net_unit"
#

