# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.16)
# Database: crawl_operations
# Generation Time: 2015-04-30 17:44:17 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table crawl_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crawl_jobs`;

CREATE TABLE `crawl_jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(255) NOT NULL DEFAULT '',
  `term` varchar(255) NOT NULL DEFAULT '',
  `pattern` text NOT NULL COMMENT 'searlized crawl pattern',
  `status` varchar(25) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `crawl_jobs` WRITE;
/*!40000 ALTER TABLE `crawl_jobs` DISABLE KEYS */;

INSERT INTO `crawl_jobs` (`id`, `domain_name`, `term`, `pattern`, `status`, `batch_id`)
VALUES
  (1, 'qoo10.sg', 'test', 'a:6:{s:10:\"search_url\";s:456:\"http://list.qoo10.sg/s/?gdlc_cd=&gdmc_cd=&gdsc_cd=&delivery_group_no=&sell_cust_no=&bundle_delivery=&bundle_policy=&keywordArrLength=1&keyword=&within_keyword_auto_change=&keyword_hist=[[CRAWL_SEARCH_TERM]]&hidden_div_height=69&curPage=[[CRAWL_SEARCH_PAGE_NUMBER]]&filterDelivery=NNNNNNNN&flt_pri_idx=0&flt_tab_idx=0&sortType=SORT_RANK_POINT&dispType=LIST&pageSize=60&partial=off&[[CRAWL_EXTRA_RULED_PARAM:paging_value|SUBTRACT:CRAWL_SEARCH_PAGE_NUMBER:1]]\";s:4:\"type\";s:25:\"PAGINATED_SEARCH_MAX_PAGE\";s:8:\"max_page\";i:276;s:7:\"wrapper\";s:16:\".bd_lst dd tbody\";s:4:\"item\";s:22:\"td.details p.subject a\";s:6:\"seller\";s:16:\"td.seller a.name\";}', 'ready', 1);

/*!40000 ALTER TABLE `crawl_jobs` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table crawl_results
# ------------------------------------------------------------

DROP TABLE IF EXISTS `crawl_results`;

CREATE TABLE `crawl_results` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(11) unsigned NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_url` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_rating` varchar(255) DEFAULT NULL,
  `breadcrumbs` varchar(255) DEFAULT NULL,
  `breadcrumb_2` varchar(255) DEFAULT NULL,
  `breadcrumb_3` varchar(255) DEFAULT NULL,
  `breadcrumb_4` varchar(255) DEFAULT NULL,
  `breadcrumb_5` varchar(255) DEFAULT NULL,
  `breadcrumb_6` varchar(255) DEFAULT NULL,
  `breadcrumb_1_url` varchar(255) DEFAULT NULL,
  `breadcrumb_2_url` varchar(255) DEFAULT NULL,
  `breadcrumb_3_url` varchar(255) DEFAULT NULL,
  `breadcrumb_4_url` varchar(255) DEFAULT NULL,
  `breadcrumb_5_url` varchar(255) DEFAULT NULL,
  `breadcrumb_6_url` varchar(255) DEFAULT NULL,
  `marketplace_seller_name` varchar(255) DEFAULT NULL,
  `marketplace_seller_url` varchar(255) DEFAULT NULL,
  `record_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
