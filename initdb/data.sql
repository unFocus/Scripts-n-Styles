-- MariaDB dump 10.17  Distrib 10.4.8-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: wordpress
-- ------------------------------------------------------
-- Server version	10.4.8-MariaDB-1:10.4.8+maria~bionic

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_commentmeta`
--

DROP TABLE IF EXISTS `wp_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_commentmeta`
--

LOCK TABLES `wp_commentmeta` WRITE;
/*!40000 ALTER TABLE `wp_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_comments`
--

DROP TABLE IF EXISTS `wp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT 0,
  `comment_author` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT 0,
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_comments`
--

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
INSERT INTO `wp_comments` VALUES (1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2017-08-03 13:36:29','2017-08-03 13:36:29','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com\">Gravatar</a>.',0,'1','','',0,0);
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_links`
--

DROP TABLE IF EXISTS `wp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT 1,
  `link_rating` int(11) NOT NULL DEFAULT 0,
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_links`
--

LOCK TABLES `wp_links` WRITE;
/*!40000 ALTER TABLE `wp_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=308 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_options`
--

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` VALUES (1,'siteurl','http://scriptsnstyles.test','yes'),(2,'home','http://scriptsnstyles.test','yes'),(3,'blogname','Scripts n Styles dev environment','yes'),(4,'blogdescription','Just another test site','yes'),(5,'users_can_register','0','yes'),(6,'admin_email','admin@example.com','yes'),(7,'start_of_week','1','yes'),(8,'use_balanceTags','0','yes'),(9,'use_smilies','1','yes'),(10,'require_name_email','1','yes'),(11,'comments_notify','1','yes'),(12,'posts_per_rss','10','yes'),(13,'rss_use_excerpt','0','yes'),(14,'mailserver_url','mail.example.com','yes'),(15,'mailserver_login','login@example.com','yes'),(16,'mailserver_pass','password','yes'),(17,'mailserver_port','110','yes'),(18,'default_category','1','yes'),(19,'default_comment_status','open','yes'),(20,'default_ping_status','open','yes'),(21,'default_pingback_flag','1','yes'),(22,'posts_per_page','10','yes'),(23,'date_format','F j, Y','yes'),(24,'time_format','g:i a','yes'),(25,'links_updated_date_format','F j, Y g:i a','yes'),(26,'comment_moderation','0','yes'),(27,'moderation_notify','1','yes'),(28,'permalink_structure','/%year%/%monthnum%/%day%/%postname%/','yes'),(29,'rewrite_rules','a:90:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:58:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:68:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:88:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:64:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:53:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$\";s:91:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:85:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1\";s:77:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:65:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]\";s:61:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:53:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}','yes'),(30,'hack_file','0','yes'),(31,'blog_charset','UTF-8','yes'),(32,'moderation_keys','','no'),(33,'active_plugins','a:1:{i:1;s:37:\"scripts-n-styles/scripts-n-styles.php\";}','yes'),(34,'category_base','','yes'),(35,'ping_sites','http://rpc.pingomatic.com/','yes'),(36,'comment_max_links','2','yes'),(37,'gmt_offset','','yes'),(38,'default_email_category','1','yes'),(39,'recently_edited','a:5:{i:0;s:82:\"/var/www/html/wp-content/plugins/scripts-n-styles/theme/scripts-n-styles/style.css\";i:2;s:83:\"/var/www/html/wp-content/plugins/scripts-n-styles/theme/scripts-n-styles/readme.txt\";i:3;s:70:\"/var/www/html/wp-content/plugins/scripts-n-styles/scripts-n-styles.php\";i:4;s:82:\"/var/www/html/wp-content/plugins/scripts-n-styles/theme/scripts-n-styles/test.json\";i:5;s:66:\"/var/www/html/wp-content/plugins/scripts-n-styles/css/meta-box.css\";}','no'),(40,'template','scripts-n-styles','yes'),(41,'stylesheet','scripts-n-styles','yes'),(42,'comment_whitelist','1','yes'),(43,'blacklist_keys','','no'),(44,'comment_registration','0','yes'),(45,'html_type','text/html','yes'),(46,'use_trackback','0','yes'),(47,'default_role','subscriber','yes'),(48,'db_version','38590','yes'),(49,'uploads_use_yearmonth_folders','1','yes'),(50,'upload_path','','yes'),(51,'blog_public','1','yes'),(52,'default_link_category','2','yes'),(53,'show_on_front','posts','yes'),(54,'tag_base','','yes'),(55,'show_avatars','1','yes'),(56,'avatar_rating','G','yes'),(57,'upload_url_path','','yes'),(58,'thumbnail_size_w','150','yes'),(59,'thumbnail_size_h','150','yes'),(60,'thumbnail_crop','1','yes'),(61,'medium_size_w','300','yes'),(62,'medium_size_h','300','yes'),(63,'avatar_default','mystery','yes'),(64,'large_size_w','1024','yes'),(65,'large_size_h','1024','yes'),(66,'image_default_link_type','none','yes'),(67,'image_default_size','','yes'),(68,'image_default_align','','yes'),(69,'close_comments_for_old_posts','0','yes'),(70,'close_comments_days_old','14','yes'),(71,'thread_comments','1','yes'),(72,'thread_comments_depth','5','yes'),(73,'page_comments','0','yes'),(74,'comments_per_page','50','yes'),(75,'default_comments_page','newest','yes'),(76,'comment_order','asc','yes'),(77,'sticky_posts','a:0:{}','yes'),(78,'widget_categories','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(79,'widget_text','a:8:{i:1;a:0:{}i:2;a:4:{s:5:\"title\";s:31:\"Text Widget - Main Widgets Area\";s:4:\"text\";s:39:\"[hoops name=\"test-02\"]\r\n<div>asdf</div>\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}i:3;a:4:{s:5:\"title\";s:33:\"Text Widget 2 - Main Widgets Area\";s:4:\"text\";s:54:\"asdfgasgasasdf\r\n\r\n<style></style>\r\n\r\n<script></script>\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}i:4;a:4:{s:5:\"title\";s:35:\"Text Widget - Inactive Widgets Area\";s:4:\"text\";s:4:\"asdf\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}s:12:\"_multiwidget\";i:1;i:5;a:4:{s:5:\"title\";s:36:\"Hoops Widget - Inactive Widgets Area\";s:4:\"text\";s:6:\"zfgadg\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}i:6;a:4:{s:5:\"title\";s:32:\"Hoops Widget - Main Widgets Area\";s:4:\"text\";s:39:\"[hoops name=\"test-02\"]\r\n<div>asdf</div>\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}i:7;a:4:{s:5:\"title\";s:34:\"Hoops Widget 2 - Main Widgets Area\";s:4:\"text\";s:5:\"asdfa\";s:6:\"filter\";b:1;s:6:\"visual\";b:1;}}','yes'),(80,'widget_rss','a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}','yes'),(81,'uninstall_plugins','a:0:{}','no'),(82,'timezone_string','America/New_York','yes'),(83,'page_for_posts','0','yes'),(84,'page_on_front','0','yes'),(85,'default_post_format','0','yes'),(86,'link_manager_enabled','0','yes'),(87,'finished_splitting_shared_terms','1','yes'),(88,'site_icon','0','yes'),(89,'medium_large_size_w','768','yes'),(90,'medium_large_size_h','0','yes'),(91,'initial_db_version','38590','yes'),(92,'wp_user_roles','a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:61:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}','yes'),(93,'fresh_site','0','yes'),(94,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(95,'widget_recent-posts','a:2:{i:2;a:3:{s:5:\"title\";s:39:\"Recent Posts Widget - Main Widgets Area\";s:6:\"number\";i:5;s:9:\"show_date\";b:0;}s:12:\"_multiwidget\";i:1;}','yes'),(96,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(97,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(98,'widget_meta','a:2:{i:2;a:1:{s:5:\"title\";s:31:\"Meta Widget - Main Widgets Area\";}s:12:\"_multiwidget\";i:1;}','yes'),(99,'sidebars_widgets','a:3:{s:19:\"wp_inactive_widgets\";a:3:{i:0;s:6:\"text-5\";i:1;s:13:\"custom_html-4\";i:2;s:6:\"text-4\";}s:9:\"sidebar-1\";a:9:{i:0;s:14:\"recent-posts-2\";i:1;s:10:\"nav_menu-2\";i:2;s:6:\"text-6\";i:3;s:6:\"text-2\";i:4;s:13:\"custom_html-2\";i:5;s:6:\"meta-2\";i:6;s:13:\"custom_html-3\";i:7;s:6:\"text-3\";i:8;s:6:\"text-7\";}s:13:\"array_version\";i:3;}','yes'),(100,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(101,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(102,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(103,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(104,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(105,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(106,'widget_nav_menu','a:2:{i:2;a:2:{s:5:\"title\";s:38:\"Custom Menu Widget - Main Widgets Area\";s:8:\"nav_menu\";i:2;}s:12:\"_multiwidget\";i:1;}','yes'),(107,'cron','a:5:{i:1569271446;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1569288990;a:3:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1569289487;a:1:{s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1569332199;a:1:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}s:7:\"version\";i:2;}','yes'),(108,'theme_mods_twentyseventeen','a:2:{s:18:\"custom_css_post_id\";i:-1;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1501967142;s:4:\"data\";a:4:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:9:\"sidebar-2\";a:0:{}s:9:\"sidebar-3\";a:0:{}}}}','yes'),(119,'widget_custom_html','a:4:{i:2;a:2:{s:5:\"title\";s:38:\"Custom HTML Widget - Main Widgets Area\";s:7:\"content\";s:22:\"[hoops name=\"test-02\"]\";}i:3;a:2:{s:5:\"title\";s:40:\"Custom HTML Widget 2 - Main Widgets Area\";s:7:\"content\";s:22:\"<p>\r\n	asdfas;ljf\r\n</p>\";}i:4;a:2:{s:5:\"title\";s:42:\"Custom HTML Widget - Inactive Widgets Area\";s:7:\"content\";s:7:\"asfasdf\";}s:12:\"_multiwidget\";i:1;}','yes'),(122,'auto_core_update_notified','a:4:{s:4:\"type\";s:7:\"success\";s:5:\"email\";s:17:\"admin@example.com\";s:7:\"version\";s:15:\"4.9-alpha-41387\";s:9:\"timestamp\";i:1505828361;}','no'),(126,'can_compress_scripts','0','no'),(141,'recently_activated','a:0:{}','yes'),(148,'SnS_options','a:15:{s:7:\"version\";s:13:\"4.0.0-alpha-2\";s:7:\"metabox\";s:2:\"no\";s:12:\"hoops_widget\";s:3:\"yes\";s:21:\"delete_data_uninstall\";s:2:\"no\";s:8:\"cm_theme\";s:8:\"ambiance\";s:5:\"hoops\";a:1:{s:10:\"shortcodes\";a:3:{s:7:\"test-01\";s:18:\"<div>test 01</div>\";s:7:\"test-02\";s:18:\"<div>test 02</div>\";s:7:\"test-03\";s:18:\"<div>test 03</div>\";}}s:4:\"less\";s:61:\"@width: 900px;\r\nhtml {\r\n  body {\r\n	max-width: @width;\r\n  }\r\n}\";s:8:\"compiled\";s:37:\"html body {\r\n  max-width: 900px;\r\n}\r\n\";s:6:\"styles\";s:44:\"body {\r\n  padding: 50px;\r\n  margin: auto;\r\n}\";s:6:\"coffee\";s:38:\"console.log \'from global coffeescript\'\";s:15:\"coffee_compiled\";s:75:\"(function() {\r\n  console.log(\'from global coffeescript\');\r\n}).call(this);\r\n\";s:15:\"scripts_in_head\";s:32:\"console.log(\'from global head\');\";s:7:\"scripts\";s:37:\"console.log(\'from global endofbody\');\";s:6:\"themes\";a:1:{s:16:\"scripts-n-styles\";a:2:{s:4:\"less\";a:4:{s:14:\"variables.less\";s:12:\"@test: #fdf;\";s:11:\"mixins.less\";s:36:\".mixin-test() {\r\n	display: block;\r\n}\";s:10:\"theme.less\";s:26:\"body {\r\n	.mixin-test();\r\n}\";s:12:\"content.less\";s:34:\".content {\r\n	background: @test;\r\n}\";}s:8:\"compiled\";s:66:\"body {\r\n  display: block;\r\n}\r\n.content {\r\n  background: #fdf;\r\n}\r\n\";}}s:15:\"enqueue_scripts\";a:1:{i:0;s:8:\"thickbox\";}}','yes'),(156,'WPLANG','','yes'),(190,'template_root','/plugins/scripts-n-styles/theme','yes'),(191,'stylesheet_root','/plugins/scripts-n-styles/theme','yes'),(192,'current_theme','Scripts n Styles','yes'),(193,'theme_mods_scripts-n-styles','a:3:{i:0;b:0;s:18:\"custom_css_post_id\";i:-1;s:18:\"nav_menu_locations\";a:1:{s:7:\"primary\";i:2;}}','yes'),(194,'theme_switched','','yes'),(199,'nav_menu_options','a:2:{i:0;b:0;s:8:\"auto_add\";a:0:{}}','yes'),(200,'category_children','a:0:{}','yes'),(272,'_site_transient_update_core','O:8:\"stdClass\":4:{s:7:\"updates\";a:3:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:7:\"upgrade\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.2.3.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.2.3.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-5.2.3-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-5.2.3-new-bundled.zip\";s:7:\"partial\";b:0;s:8:\"rollback\";b:0;}s:7:\"current\";s:5:\"5.2.3\";s:7:\"version\";s:5:\"5.2.3\";s:11:\"php_version\";s:6:\"5.6.20\";s:13:\"mysql_version\";s:3:\"5.0\";s:11:\"new_bundled\";s:3:\"5.0\";s:15:\"partial_version\";s:0:\"\";}i:1;O:8:\"stdClass\":11:{s:8:\"response\";s:10:\"autoupdate\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.2.3.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.2.3.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-5.2.3-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-5.2.3-new-bundled.zip\";s:7:\"partial\";b:0;s:8:\"rollback\";b:0;}s:7:\"current\";s:5:\"5.2.3\";s:7:\"version\";s:5:\"5.2.3\";s:11:\"php_version\";s:6:\"5.6.20\";s:13:\"mysql_version\";s:3:\"5.0\";s:11:\"new_bundled\";s:3:\"5.0\";s:15:\"partial_version\";s:0:\"\";s:9:\"new_files\";s:1:\"1\";}i:2;O:8:\"stdClass\":11:{s:8:\"response\";s:10:\"autoupdate\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.1.2.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-5.1.2.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-5.1.2-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-5.1.2-new-bundled.zip\";s:7:\"partial\";b:0;s:8:\"rollback\";b:0;}s:7:\"current\";s:5:\"5.1.2\";s:7:\"version\";s:5:\"5.1.2\";s:11:\"php_version\";s:5:\"5.2.4\";s:13:\"mysql_version\";s:3:\"5.0\";s:11:\"new_bundled\";s:3:\"5.0\";s:15:\"partial_version\";s:0:\"\";s:9:\"new_files\";s:1:\"1\";}}s:12:\"last_checked\";i:1569271086;s:15:\"version_checked\";s:5:\"5.0.6\";s:12:\"translations\";a:0:{}}','no'),(273,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),(296,'new_admin_email','admin@example.com','yes'),(304,'_site_transient_timeout_theme_roots','1569272887','no'),(305,'_site_transient_theme_roots','a:5:{s:16:\"scripts-n-styles\";s:31:\"/plugins/scripts-n-styles/theme\";s:13:\"twentyfifteen\";s:7:\"/themes\";s:14:\"twentynineteen\";s:7:\"/themes\";s:15:\"twentyseventeen\";s:7:\"/themes\";s:13:\"twentysixteen\";s:7:\"/themes\";}','no'),(306,'_site_transient_update_themes','O:8:\"stdClass\":4:{s:12:\"last_checked\";i:1569271087;s:7:\"checked\";a:5:{s:16:\"scripts-n-styles\";s:11:\"4.0.0-alpha\";s:13:\"twentyfifteen\";s:3:\"2.2\";s:14:\"twentynineteen\";s:3:\"1.1\";s:15:\"twentyseventeen\";s:3:\"1.9\";s:13:\"twentysixteen\";s:3:\"1.7\";}s:8:\"response\";a:4:{s:13:\"twentyfifteen\";a:6:{s:5:\"theme\";s:13:\"twentyfifteen\";s:11:\"new_version\";s:3:\"2.5\";s:3:\"url\";s:43:\"https://wordpress.org/themes/twentyfifteen/\";s:7:\"package\";s:59:\"https://downloads.wordpress.org/theme/twentyfifteen.2.5.zip\";s:8:\"requires\";s:3:\"4.1\";s:12:\"requires_php\";s:5:\"5.2.4\";}s:14:\"twentynineteen\";a:6:{s:5:\"theme\";s:14:\"twentynineteen\";s:11:\"new_version\";s:3:\"1.4\";s:3:\"url\";s:44:\"https://wordpress.org/themes/twentynineteen/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/theme/twentynineteen.1.4.zip\";s:8:\"requires\";s:5:\"4.9.6\";s:12:\"requires_php\";s:5:\"5.2.4\";}s:15:\"twentyseventeen\";a:6:{s:5:\"theme\";s:15:\"twentyseventeen\";s:11:\"new_version\";s:3:\"2.2\";s:3:\"url\";s:45:\"https://wordpress.org/themes/twentyseventeen/\";s:7:\"package\";s:61:\"https://downloads.wordpress.org/theme/twentyseventeen.2.2.zip\";s:8:\"requires\";s:3:\"4.7\";s:12:\"requires_php\";s:5:\"5.2.4\";}s:13:\"twentysixteen\";a:6:{s:5:\"theme\";s:13:\"twentysixteen\";s:11:\"new_version\";s:3:\"2.0\";s:3:\"url\";s:43:\"https://wordpress.org/themes/twentysixteen/\";s:7:\"package\";s:59:\"https://downloads.wordpress.org/theme/twentysixteen.2.0.zip\";s:8:\"requires\";s:3:\"4.4\";s:12:\"requires_php\";s:5:\"5.2.4\";}}s:12:\"translations\";a:0:{}}','no'),(307,'_site_transient_update_plugins','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1569271087;s:7:\"checked\";a:8:{s:34:\"advanced-custom-fields-pro/acf.php\";s:5:\"5.7.1\";s:33:\"classic-editor/classic-editor.php\";s:3:\"1.5\";s:23:\"debug-bar/debug-bar.php\";s:3:\"1.0\";s:23:\"gutenberg/gutenberg.php\";s:5:\"6.5.0\";s:65:\"html-editor-syntax-highlighter/html-editor-syntax-highlighter.php\";s:5:\"2.2.6\";s:37:\"scripts-n-styles/scripts-n-styles.php\";s:13:\"4.0.0-alpha-3\";s:41:\"wordpress-importer/wordpress-importer.php\";s:5:\"0.6.4\";s:24:\"wordpress-seo/wp-seo.php\";s:4:\"12.1\";}s:8:\"response\";a:1:{s:65:\"html-editor-syntax-highlighter/html-editor-syntax-highlighter.php\";O:8:\"stdClass\":12:{s:2:\"id\";s:44:\"w.org/plugins/html-editor-syntax-highlighter\";s:4:\"slug\";s:30:\"html-editor-syntax-highlighter\";s:6:\"plugin\";s:65:\"html-editor-syntax-highlighter/html-editor-syntax-highlighter.php\";s:11:\"new_version\";s:5:\"2.4.2\";s:3:\"url\";s:61:\"https://wordpress.org/plugins/html-editor-syntax-highlighter/\";s:7:\"package\";s:79:\"https://downloads.wordpress.org/plugin/html-editor-syntax-highlighter.2.4.2.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:83:\"https://ps.w.org/html-editor-syntax-highlighter/assets/icon-256x256.png?rev=2013780\";s:2:\"1x\";s:75:\"https://ps.w.org/html-editor-syntax-highlighter/assets/icon.svg?rev=2013780\";s:3:\"svg\";s:75:\"https://ps.w.org/html-editor-syntax-highlighter/assets/icon.svg?rev=2013780\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:86:\"https://ps.w.org/html-editor-syntax-highlighter/assets/banner-1544x500.png?rev=2013780\";s:2:\"1x\";s:85:\"https://ps.w.org/html-editor-syntax-highlighter/assets/banner-772x250.png?rev=2013780\";}s:11:\"banners_rtl\";a:0:{}s:6:\"tested\";s:5:\"5.2.3\";s:12:\"requires_php\";b:0;s:13:\"compatibility\";O:8:\"stdClass\":0:{}}}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:6:{s:33:\"classic-editor/classic-editor.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:28:\"w.org/plugins/classic-editor\";s:4:\"slug\";s:14:\"classic-editor\";s:6:\"plugin\";s:33:\"classic-editor/classic-editor.php\";s:11:\"new_version\";s:3:\"1.5\";s:3:\"url\";s:45:\"https://wordpress.org/plugins/classic-editor/\";s:7:\"package\";s:61:\"https://downloads.wordpress.org/plugin/classic-editor.1.5.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/classic-editor/assets/icon-256x256.png?rev=1998671\";s:2:\"1x\";s:67:\"https://ps.w.org/classic-editor/assets/icon-128x128.png?rev=1998671\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:70:\"https://ps.w.org/classic-editor/assets/banner-1544x500.png?rev=1998671\";s:2:\"1x\";s:69:\"https://ps.w.org/classic-editor/assets/banner-772x250.png?rev=1998676\";}s:11:\"banners_rtl\";a:0:{}}s:23:\"debug-bar/debug-bar.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:23:\"w.org/plugins/debug-bar\";s:4:\"slug\";s:9:\"debug-bar\";s:6:\"plugin\";s:23:\"debug-bar/debug-bar.php\";s:11:\"new_version\";s:3:\"1.0\";s:3:\"url\";s:40:\"https://wordpress.org/plugins/debug-bar/\";s:7:\"package\";s:56:\"https://downloads.wordpress.org/plugin/debug-bar.1.0.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:62:\"https://ps.w.org/debug-bar/assets/icon-256x256.png?rev=1908362\";s:2:\"1x\";s:54:\"https://ps.w.org/debug-bar/assets/icon.svg?rev=1908362\";s:3:\"svg\";s:54:\"https://ps.w.org/debug-bar/assets/icon.svg?rev=1908362\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:65:\"https://ps.w.org/debug-bar/assets/banner-1544x500.png?rev=1365496\";s:2:\"1x\";s:64:\"https://ps.w.org/debug-bar/assets/banner-772x250.png?rev=1365496\";}s:11:\"banners_rtl\";a:0:{}}s:23:\"gutenberg/gutenberg.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:23:\"w.org/plugins/gutenberg\";s:4:\"slug\";s:9:\"gutenberg\";s:6:\"plugin\";s:23:\"gutenberg/gutenberg.php\";s:11:\"new_version\";s:5:\"6.5.0\";s:3:\"url\";s:40:\"https://wordpress.org/plugins/gutenberg/\";s:7:\"package\";s:58:\"https://downloads.wordpress.org/plugin/gutenberg.6.5.0.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:62:\"https://ps.w.org/gutenberg/assets/icon-256x256.jpg?rev=1776042\";s:2:\"1x\";s:62:\"https://ps.w.org/gutenberg/assets/icon-128x128.jpg?rev=1776042\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:65:\"https://ps.w.org/gutenberg/assets/banner-1544x500.jpg?rev=1718710\";s:2:\"1x\";s:64:\"https://ps.w.org/gutenberg/assets/banner-772x250.jpg?rev=1718710\";}s:11:\"banners_rtl\";a:0:{}}s:37:\"scripts-n-styles/scripts-n-styles.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:30:\"w.org/plugins/scripts-n-styles\";s:4:\"slug\";s:16:\"scripts-n-styles\";s:6:\"plugin\";s:37:\"scripts-n-styles/scripts-n-styles.php\";s:11:\"new_version\";s:5:\"3.5.1\";s:3:\"url\";s:47:\"https://wordpress.org/plugins/scripts-n-styles/\";s:7:\"package\";s:65:\"https://downloads.wordpress.org/plugin/scripts-n-styles.3.5.1.zip\";s:5:\"icons\";a:1:{s:7:\"default\";s:67:\"https://s.w.org/plugins/geopattern-icon/scripts-n-styles_46515d.svg\";}s:7:\"banners\";a:1:{s:2:\"1x\";s:70:\"https://ps.w.org/scripts-n-styles/assets/banner-772x250.png?rev=539209\";}s:11:\"banners_rtl\";a:0:{}}s:41:\"wordpress-importer/wordpress-importer.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:32:\"w.org/plugins/wordpress-importer\";s:4:\"slug\";s:18:\"wordpress-importer\";s:6:\"plugin\";s:41:\"wordpress-importer/wordpress-importer.php\";s:11:\"new_version\";s:5:\"0.6.4\";s:3:\"url\";s:49:\"https://wordpress.org/plugins/wordpress-importer/\";s:7:\"package\";s:67:\"https://downloads.wordpress.org/plugin/wordpress-importer.0.6.4.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:71:\"https://ps.w.org/wordpress-importer/assets/icon-256x256.png?rev=1908375\";s:2:\"1x\";s:63:\"https://ps.w.org/wordpress-importer/assets/icon.svg?rev=1908375\";s:3:\"svg\";s:63:\"https://ps.w.org/wordpress-importer/assets/icon.svg?rev=1908375\";}s:7:\"banners\";a:1:{s:2:\"1x\";s:72:\"https://ps.w.org/wordpress-importer/assets/banner-772x250.png?rev=547654\";}s:11:\"banners_rtl\";a:0:{}}s:24:\"wordpress-seo/wp-seo.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:27:\"w.org/plugins/wordpress-seo\";s:4:\"slug\";s:13:\"wordpress-seo\";s:6:\"plugin\";s:24:\"wordpress-seo/wp-seo.php\";s:11:\"new_version\";s:4:\"12.1\";s:3:\"url\";s:44:\"https://wordpress.org/plugins/wordpress-seo/\";s:7:\"package\";s:61:\"https://downloads.wordpress.org/plugin/wordpress-seo.12.1.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:66:\"https://ps.w.org/wordpress-seo/assets/icon-256x256.png?rev=1834347\";s:2:\"1x\";s:58:\"https://ps.w.org/wordpress-seo/assets/icon.svg?rev=1946641\";s:3:\"svg\";s:58:\"https://ps.w.org/wordpress-seo/assets/icon.svg?rev=1946641\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/wordpress-seo/assets/banner-1544x500.png?rev=1843435\";s:2:\"1x\";s:68:\"https://ps.w.org/wordpress-seo/assets/banner-772x250.png?rev=1843435\";}s:11:\"banners_rtl\";a:2:{s:2:\"2x\";s:73:\"https://ps.w.org/wordpress-seo/assets/banner-1544x500-rtl.png?rev=1843435\";s:2:\"1x\";s:72:\"https://ps.w.org/wordpress-seo/assets/banner-772x250-rtl.png?rev=1843435\";}}}}','no');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_postmeta`
--

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default'),(2,1,'_edit_lock','1501968308:2'),(3,1,'_SnS','a:3:{s:6:\"styles\";a:4:{s:12:\"classes_body\";s:9:\"body-test\";s:12:\"classes_post\";s:9:\"post-test\";s:6:\"styles\";s:130:\".mceContentBody {\r\n	margin: 30px;\r\n}\r\n.padded {\r\n	padding: 10px;\r\n}\r\n.blue {color:blue;}\r\n.red {color:red;}\r\n.green {color:green;}\";s:11:\"classes_mce\";a:4:{s:8:\"red span\";a:3:{s:5:\"title\";s:8:\"red span\";s:7:\"classes\";s:3:\"red\";s:6:\"inline\";s:4:\"span\";}s:6:\"blue p\";a:3:{s:5:\"title\";s:6:\"blue p\";s:7:\"classes\";s:4:\"blue\";s:5:\"block\";s:1:\"p\";}s:10:\"padded div\";a:4:{s:5:\"title\";s:10:\"padded div\";s:7:\"classes\";s:6:\"padded\";s:5:\"block\";s:3:\"div\";s:7:\"wrapper\";s:4:\"true\";}s:5:\"green\";a:3:{s:5:\"title\";s:5:\"green\";s:7:\"classes\";s:5:\"green\";s:8:\"selector\";s:3:\"div\";}}}s:10:\"shortcodes\";a:2:{s:9:\"post-test\";s:20:\"<div>post test</div>\";s:7:\"test-01\";s:30:\"<div>test 01 overwritten</div>\";}s:7:\"scripts\";a:3:{s:15:\"scripts_in_head\";s:35:\"console.log(\'from this post head\');\";s:7:\"scripts\";s:37:\"console.log(\'from this post footer\');\";s:15:\"enqueue_scripts\";a:1:{i:0;s:10:\"underscore\";}}}'),(4,1,'_edit_last','2'),(11,2,'_edit_lock','1501968370:2'),(14,2,'_SnS','a:3:{s:10:\"shortcodes\";a:2:{s:7:\"test-02\";s:30:\"<div>test 02 overwritten</div>\";s:9:\"page-test\";s:20:\"<div>page test</div>\";}s:6:\"styles\";a:4:{s:11:\"classes_mce\";a:4:{s:8:\"red span\";a:3:{s:5:\"title\";s:8:\"red span\";s:7:\"classes\";s:3:\"red\";s:6:\"inline\";s:4:\"span\";}s:6:\"blue p\";a:3:{s:5:\"title\";s:6:\"blue p\";s:7:\"classes\";s:4:\"blue\";s:5:\"block\";s:1:\"p\";}s:10:\"padded div\";a:4:{s:5:\"title\";s:10:\"padded div\";s:7:\"classes\";s:6:\"padded\";s:5:\"block\";s:3:\"div\";s:7:\"wrapper\";s:4:\"true\";}s:5:\"green\";a:3:{s:5:\"title\";s:5:\"green\";s:7:\"classes\";s:5:\"green\";s:8:\"selector\";s:3:\"div\";}}s:12:\"classes_body\";s:9:\"body-test\";s:12:\"classes_post\";s:9:\"post-test\";s:6:\"styles\";s:130:\".mceContentBody {\r\n	margin: 30px;\r\n}\r\n.padded {\r\n	padding: 10px;\r\n}\r\n.blue {color:blue;}\r\n.red {color:red;}\r\n.green {color:green;}\";}s:7:\"scripts\";a:3:{s:15:\"scripts_in_head\";s:35:\"console.log(\'from this post head\');\";s:7:\"scripts\";s:37:\"console.log(\'from this post footer\');\";s:15:\"enqueue_scripts\";a:1:{i:0;s:10:\"underscore\";}}}'),(15,2,'_edit_last','2'),(16,13,'_menu_item_type','custom'),(17,13,'_menu_item_menu_item_parent','0'),(18,13,'_menu_item_object_id','13'),(19,13,'_menu_item_object','custom'),(20,13,'_menu_item_target',''),(21,13,'_menu_item_classes','a:1:{i:0;s:0:\"\";}'),(22,13,'_menu_item_xfn',''),(23,13,'_menu_item_url','http://localhost/'),(25,14,'_menu_item_type','post_type'),(26,14,'_menu_item_menu_item_parent','0'),(27,14,'_menu_item_object_id','2'),(28,14,'_menu_item_object','page'),(29,14,'_menu_item_target',''),(30,14,'_menu_item_classes','a:1:{i:0;s:0:\"\";}'),(31,14,'_menu_item_xfn',''),(32,14,'_menu_item_url','');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_posts`
--

DROP TABLE IF EXISTS `wp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `guid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_posts`
--

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;
INSERT INTO `wp_posts` VALUES (1,2,'2017-08-03 13:36:29','2017-08-03 13:36:29','<div class=\"padded green\">\r\n\r\nWelcome to <span class=\"red\">WordPress</span>. This is your first post. Edit or delete it, then start writing!\r\n<p class=\"blue\">Blue paragraph.</p>\r\n\r\n</div>\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"page-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Hello world!','','publish','open','open','','hello-world','','','2017-08-05 17:23:33','2017-08-05 21:23:33','',0,'http://localhost/?p=1',0,'post','',1),(2,2,'2017-08-03 13:36:29','2017-08-03 13:36:29','This is an <span class=\"red\">example</span> page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\r\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin\' caught in the rain.)</blockquote>\r\n<p class=\"blue\">...or something like this:</p>\r\n\r\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\r\n<div class=\"padded green\">\r\n\r\nAs a new WordPress user, you should go to <a href=\"http://localhost/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!\r\n\r\n</div>\r\n&nbsp;\r\n\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"page-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Sample Page','','publish','closed','open','','sample-page','','','2017-08-05 17:28:31','2017-08-05 21:28:31','',0,'http://localhost/?page_id=2',0,'page','',0),(3,2,'2017-08-03 13:36:39','0000-00-00 00:00:00','','Auto Draft','','auto-draft','open','open','','','','','2017-08-03 13:36:39','0000-00-00 00:00:00','',0,'http://localhost/?p=3',0,'post','',0),(4,2,'2017-08-05 17:01:47','0000-00-00 00:00:00','','Auto Draft','','auto-draft','open','open','','','','','2017-08-05 17:01:47','0000-00-00 00:00:00','',0,'http://localhost/?p=4',0,'post','',0),(5,2,'2017-08-05 17:18:59','2017-08-05 21:18:59','<div class=\"padded\">\n\nWelcome to WordPress. This is your first post. Edit or delete it, then start writing!\n\n</div>\n[hoops name=\"post-test\"]\n\n[hoops name=\"test-01\"]\n\n[hoops name=\"test-02\"]\n\n[hoops name=\"test-03\"]','Hello world!','','inherit','closed','closed','','1-autosave-v1','','','2017-08-05 17:18:59','2017-08-05 21:18:59','',1,'http://localhost/2017/08/05/1-autosave-v1/',0,'revision','',0),(6,2,'2017-08-05 17:13:58','2017-08-05 21:13:58','Welcome to WordPress. This is your first post. Edit or delete it, then start writing!\r\n\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Hello world!','','inherit','closed','closed','','1-revision-v1','','','2017-08-05 17:13:58','2017-08-05 21:13:58','',1,'http://localhost/2017/08/05/1-revision-v1/',0,'revision','',0),(7,2,'2017-08-05 17:20:54','2017-08-05 21:20:54','<div class=\"padded green\">\r\n<p class=\"blue\">Welcome to <span class=\"red\">WordPress</span>. This is your first post. Edit or delete it, then start writing!</p>\r\n\r\n</div>\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Hello world!','','inherit','closed','closed','','1-revision-v1','','','2017-08-05 17:20:54','2017-08-05 21:20:54','',1,'http://localhost/2017/08/05/1-revision-v1/',0,'revision','',0),(8,2,'2017-08-05 17:21:53','2017-08-05 21:21:53','<div class=\"padded green\">\r\n\r\nWelcome to <span class=\"red\">WordPress</span>. This is your first post. Edit or delete it, then start writing!\r\n<p class=\"blue\">Blue paragraph.</p>\r\n\r\n</div>\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Hello world!','','inherit','closed','closed','','1-revision-v1','','','2017-08-05 17:21:53','2017-08-05 21:21:53','',1,'http://localhost/2017/08/05/1-revision-v1/',0,'revision','',0),(9,2,'2017-08-05 17:25:21','2017-08-05 21:25:21','This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin\' caught in the rain.)</blockquote>\n...or something like this:\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\nAs a new WordPress user, you should go to <a href=\"http://localhost/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!\n\n&nbsp;\n\n[hoops name=\"post-test\"]\n\n[hoops name=\"page-test\"]\n\n[hoops name=\"test-01\"]\n\n[hoops name=\"test-02\"]\n\n[hoops name=\"test-03\"]','Sample Page','','inherit','closed','closed','','2-autosave-v1','','','2017-08-05 17:25:21','2017-08-05 21:25:21','',2,'http://localhost/2017/08/05/2-autosave-v1/',0,'revision','',0),(10,2,'2017-08-05 17:23:33','2017-08-05 21:23:33','<div class=\"padded green\">\r\n\r\nWelcome to <span class=\"red\">WordPress</span>. This is your first post. Edit or delete it, then start writing!\r\n<p class=\"blue\">Blue paragraph.</p>\r\n\r\n</div>\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"page-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Hello world!','','inherit','closed','closed','','1-revision-v1','','','2017-08-05 17:23:33','2017-08-05 21:23:33','',1,'http://localhost/2017/08/05/1-revision-v1/',0,'revision','',0),(11,2,'2017-08-05 17:27:27','2017-08-05 21:27:27','This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\r\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin\' caught in the rain.)</blockquote>\r\n...or something like this:\r\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\r\nAs a new WordPress user, you should go to <a href=\"http://localhost/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!\r\n\r\n&nbsp;\r\n\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"page-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Sample Page','','inherit','closed','closed','','2-revision-v1','','','2017-08-05 17:27:27','2017-08-05 21:27:27','',2,'http://localhost/2017/08/05/2-revision-v1/',0,'revision','',0),(12,2,'2017-08-05 17:28:31','2017-08-05 21:28:31','This is an <span class=\"red\">example</span> page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\r\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin\' caught in the rain.)</blockquote>\r\n<p class=\"blue\">...or something like this:</p>\r\n\r\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\r\n<div class=\"padded green\">\r\n\r\nAs a new WordPress user, you should go to <a href=\"http://localhost/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!\r\n\r\n</div>\r\n&nbsp;\r\n\r\n[hoops name=\"post-test\"]\r\n\r\n[hoops name=\"page-test\"]\r\n\r\n[hoops name=\"test-01\"]\r\n\r\n[hoops name=\"test-02\"]\r\n\r\n[hoops name=\"test-03\"]','Sample Page','','inherit','closed','closed','','2-revision-v1','','','2017-08-05 17:28:31','2017-08-05 21:28:31','',2,'http://localhost/2017/08/05/2-revision-v1/',0,'revision','',0),(13,2,'2017-08-05 17:29:05','2017-08-05 21:29:05','','Home','','publish','closed','closed','','home','','','2017-08-05 17:29:09','2017-08-05 21:29:09','',0,'http://localhost/?p=13',1,'nav_menu_item','',0),(14,2,'2017-08-05 17:29:05','2017-08-05 21:29:05',' ','','','publish','closed','closed','','14','','','2017-08-05 17:29:09','2017-08-05 21:29:09','',0,'http://localhost/?p=14',2,'nav_menu_item','',0),(15,2,'2017-09-15 10:56:43','0000-00-00 00:00:00','{\n    \"custom_css[scripts-n-styles]\": {\n        \"value\": \"\",\n        \"type\": \"custom_css\",\n        \"user_id\": 2\n    }\n}','','','auto-draft','closed','closed','','5839bf9c-6a74-4eb9-a2f5-85edea0ac8ae','','','2017-09-15 10:56:43','2017-09-15 14:56:43','',0,'http://localhost/?p=15',0,'customize_changeset','',0);
/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_relationships`
--

DROP TABLE IF EXISTS `wp_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_relationships`
--

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;
INSERT INTO `wp_term_relationships` VALUES (1,1,0),(13,2,0),(14,2,0);
/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_term_taxonomy`
--

DROP TABLE IF EXISTS `wp_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `count` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_term_taxonomy`
--

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
INSERT INTO `wp_term_taxonomy` VALUES (1,1,'category','',0,1),(2,2,'nav_menu','',0,2);
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_termmeta`
--

DROP TABLE IF EXISTS `wp_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_termmeta`
--

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_terms`
--

DROP TABLE IF EXISTS `wp_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_terms`
--

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;
INSERT INTO `wp_terms` VALUES (1,'Uncategorized','uncategorized',0),(2,'Menu 1','menu-1',0);
/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_usermeta`
--

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (18,2,'nickname','admin'),(19,2,'first_name',''),(20,2,'last_name',''),(21,2,'description',''),(22,2,'rich_editing','true'),(23,2,'comment_shortcuts','false'),(24,2,'admin_color','fresh'),(25,2,'use_ssl','0'),(26,2,'show_admin_bar_front','true'),(27,2,'locale',''),(28,2,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}'),(29,2,'wp_user_level','10'),(30,2,'dismissed_wp_pointers','text_widget_custom_html,wp496_privacy'),(31,2,'session_tokens','a:3:{s:64:\"f5381c4c56b7f9f08eee3be612f90380ff8a650284e66607de247fcc6b037267\";a:4:{s:10:\"expiration\";i:1543023886;s:2:\"ip\";s:10:\"172.25.0.1\";s:2:\"ua\";s:121:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36\";s:5:\"login\";i:1542851086;}s:64:\"38953e91c41bbd95d5e2fa905aa65d3224363e5b1c8889c59e9ff3c175b61194\";a:4:{s:10:\"expiration\";i:1543023954;s:2:\"ip\";s:10:\"172.25.0.1\";s:2:\"ua\";s:121:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36\";s:5:\"login\";i:1542851154;}s:64:\"19ee92f159539a5a9ee4df6dc20db433e65957aa388d70f90bea1b96078e9255\";a:4:{s:10:\"expiration\";i:1543023964;s:2:\"ip\";s:10:\"172.25.0.1\";s:2:\"ua\";s:121:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36\";s:5:\"login\";i:1542851164;}}'),(32,2,'wp_dashboard_quick_press_last_post_id','4'),(33,2,'community-events-location','a:1:{s:2:\"ip\";s:10:\"172.25.0.0\";}'),(34,2,'wp_sns_open_theme_panels','{\"variables.less\":\"no\",\"content.less\":\"no\",\"theme.less\":\"no\",\"mixins.less\":\"no\"}'),(35,2,'wp_user-settings','unfold=1&mfold=o&editor=tinymce&hidetb=1'),(36,2,'wp_user-settings-time','1505760453'),(37,2,'current_sns_tab','s0'),(38,2,'closedpostboxes_post','a:0:{}'),(39,2,'metaboxhidden_post','a:7:{i:0;s:11:\"postexcerpt\";i:1;s:13:\"trackbacksdiv\";i:2;s:10:\"postcustom\";i:3;s:16:\"commentstatusdiv\";i:4;s:11:\"commentsdiv\";i:5;s:7:\"slugdiv\";i:6;s:9:\"authordiv\";}'),(40,2,'closedpostboxes_page','a:0:{}'),(41,2,'metaboxhidden_page','a:5:{i:0;s:10:\"postcustom\";i:1;s:16:\"commentstatusdiv\";i:2;s:11:\"commentsdiv\";i:3;s:7:\"slugdiv\";i:4;s:9:\"authordiv\";}'),(42,2,'managenav-menuscolumnshidden','a:5:{i:0;s:11:\"link-target\";i:1;s:11:\"css-classes\";i:2;s:3:\"xfn\";i:3;s:11:\"description\";i:4;s:15:\"title-attribute\";}'),(43,2,'metaboxhidden_nav-menus','a:2:{i:0;s:12:\"add-post_tag\";i:1;s:15:\"add-post_format\";}'),(44,2,'show_try_gutenberg_panel','0'),(45,2,'show_welcome_panel','0');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 0,
  `display_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_users`
--

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (2,'admin','$P$BYYxSbNuT1YD4H6daHH.8aceTFoGwx1','admin','admin@example.com','','2017-08-05 21:01:38','',0,'admin');
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-23 20:39:05
