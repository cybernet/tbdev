-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Feb 27, 2006 at 10:25 PM
-- Server version: 4.1.9
-- PHP Version: 4.3.10
-- 
-- Database: `tbdev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `avps`
-- 

DROP TABLE IF EXISTS `avps`;
CREATE TABLE `avps` (
  `arg` varchar(20) NOT NULL default '',
  `value_s` text NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `avps`
-- 

INSERT INTO `avps` VALUES ('lastcleantime', '', 0, 1140625810);

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

DROP TABLE IF EXISTS `bans`;
CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `bans`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `blocks`
-- 

DROP TABLE IF EXISTS `blocks`;
CREATE TABLE `blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `blocks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` VALUES (1, 'Appz/PC ISO', 'cat_apps.png');
INSERT INTO `categories` VALUES (4, 'Games/PC ISO', 'cat_games.png');
INSERT INTO `categories` VALUES (5, 'Movies/SVCD', 'cat_movies_svcd.png');
INSERT INTO `categories` VALUES (6, 'Music', 'cat_music.png');
INSERT INTO `categories` VALUES (7, 'Episodes', 'cat_episodes.png');
INSERT INTO `categories` VALUES (9, 'XXX', 'cat_xxx.png');
INSERT INTO `categories` VALUES (12, 'Games/GBA', 'cat_games.png');
INSERT INTO `categories` VALUES (17, 'Games/PS2', 'cat_games.png');
INSERT INTO `categories` VALUES (19, 'Movies/XviD', 'cat_movies_xvid.png');
INSERT INTO `categories` VALUES (20, 'Movies/DVD-R', 'cat_movies_dvd.png');
INSERT INTO `categories` VALUES (21, 'Games/PC Rips', 'cat_games.png');
INSERT INTO `categories` VALUES (22, 'Appz/misc', 'cat_apps.png');

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  `ori_text` text NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `flagpic` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=105 ;

-- 
-- Dumping data for table `countries`
-- 

INSERT INTO `countries` VALUES (1, 'Sweden', 'sweden.gif');
INSERT INTO `countries` VALUES (2, 'United States of America', 'usa.gif');
INSERT INTO `countries` VALUES (3, 'Russia', 'russia.gif');
INSERT INTO `countries` VALUES (4, 'Finland', 'finland.gif');
INSERT INTO `countries` VALUES (5, 'Canada', 'canada.gif');
INSERT INTO `countries` VALUES (6, 'France', 'france.gif');
INSERT INTO `countries` VALUES (7, 'Germany', 'germany.gif');
INSERT INTO `countries` VALUES (8, 'China', 'china.gif');
INSERT INTO `countries` VALUES (9, 'Italy', 'italy.gif');
INSERT INTO `countries` VALUES (10, 'Denmark', 'denmark.gif');
INSERT INTO `countries` VALUES (11, 'Norway', 'norway.gif');
INSERT INTO `countries` VALUES (12, 'United Kingdom', 'uk.gif');
INSERT INTO `countries` VALUES (13, 'Ireland', 'ireland.gif');
INSERT INTO `countries` VALUES (14, 'Poland', 'poland.gif');
INSERT INTO `countries` VALUES (15, 'Netherlands', 'netherlands.gif');
INSERT INTO `countries` VALUES (16, 'Belgium', 'belgium.gif');
INSERT INTO `countries` VALUES (17, 'Japan', 'japan.gif');
INSERT INTO `countries` VALUES (18, 'Brazil', 'brazil.gif');
INSERT INTO `countries` VALUES (19, 'Argentina', 'argentina.gif');
INSERT INTO `countries` VALUES (20, 'Australia', 'australia.gif');
INSERT INTO `countries` VALUES (21, 'New Zealand', 'newzealand.gif');
INSERT INTO `countries` VALUES (23, 'Spain', 'spain.gif');
INSERT INTO `countries` VALUES (24, 'Portugal', 'portugal.gif');
INSERT INTO `countries` VALUES (25, 'Mexico', 'mexico.gif');
INSERT INTO `countries` VALUES (26, 'Singapore', 'singapore.gif');
INSERT INTO `countries` VALUES (70, 'India', 'india.gif');
INSERT INTO `countries` VALUES (65, 'Albania', 'albania.gif');
INSERT INTO `countries` VALUES (29, 'South Africa', 'southafrica.gif');
INSERT INTO `countries` VALUES (30, 'South Korea', 'southkorea.gif');
INSERT INTO `countries` VALUES (31, 'Jamaica', 'jamaica.gif');
INSERT INTO `countries` VALUES (32, 'Luxembourg', 'luxembourg.gif');
INSERT INTO `countries` VALUES (33, 'Hong Kong', 'hongkong.gif');
INSERT INTO `countries` VALUES (34, 'Belize', 'belize.gif');
INSERT INTO `countries` VALUES (35, 'Algeria', 'algeria.gif');
INSERT INTO `countries` VALUES (36, 'Angola', 'angola.gif');
INSERT INTO `countries` VALUES (37, 'Austria', 'austria.gif');
INSERT INTO `countries` VALUES (38, 'Yugoslavia', 'yugoslavia.gif');
INSERT INTO `countries` VALUES (39, 'Western Samoa', 'westernsamoa.gif');
INSERT INTO `countries` VALUES (40, 'Malaysia', 'malaysia.gif');
INSERT INTO `countries` VALUES (41, 'Dominican Republic', 'dominicanrep.gif');
INSERT INTO `countries` VALUES (42, 'Greece', 'greece.gif');
INSERT INTO `countries` VALUES (43, 'Guatemala', 'guatemala.gif');
INSERT INTO `countries` VALUES (44, 'Israel', 'israel.gif');
INSERT INTO `countries` VALUES (45, 'Pakistan', 'pakistan.gif');
INSERT INTO `countries` VALUES (46, 'Czech Republic', 'czechrep.gif');
INSERT INTO `countries` VALUES (47, 'Serbia', 'serbia.gif');
INSERT INTO `countries` VALUES (48, 'Seychelles', 'seychelles.gif');
INSERT INTO `countries` VALUES (49, 'Taiwan', 'taiwan.gif');
INSERT INTO `countries` VALUES (50, 'Puerto Rico', 'puertorico.gif');
INSERT INTO `countries` VALUES (51, 'Chile', 'chile.gif');
INSERT INTO `countries` VALUES (52, 'Cuba', 'cuba.gif');
INSERT INTO `countries` VALUES (53, 'Congo', 'congo.gif');
INSERT INTO `countries` VALUES (54, 'Afghanistan', 'afghanistan.gif');
INSERT INTO `countries` VALUES (55, 'Turkey', 'turkey.gif');
INSERT INTO `countries` VALUES (56, 'Uzbekistan', 'uzbekistan.gif');
INSERT INTO `countries` VALUES (57, 'Switzerland', 'switzerland.gif');
INSERT INTO `countries` VALUES (58, 'Kiribati', 'kiribati.gif');
INSERT INTO `countries` VALUES (59, 'Philippines', 'philippines.gif');
INSERT INTO `countries` VALUES (60, 'Burkina Faso', 'burkinafaso.gif');
INSERT INTO `countries` VALUES (61, 'Nigeria', 'nigeria.gif');
INSERT INTO `countries` VALUES (62, 'Iceland', 'iceland.gif');
INSERT INTO `countries` VALUES (63, 'Nauru', 'nauru.gif');
INSERT INTO `countries` VALUES (64, 'Slovenia', 'slovenia.gif');
INSERT INTO `countries` VALUES (66, 'Turkmenistan', 'turkmenistan.gif');
INSERT INTO `countries` VALUES (67, 'Bosnia Herzegovina', 'bosniaherzegovina.gif');
INSERT INTO `countries` VALUES (68, 'Andorra', 'andorra.gif');
INSERT INTO `countries` VALUES (69, 'Lithuania', 'lithuania.gif');
INSERT INTO `countries` VALUES (71, 'Netherlands Antilles', 'nethantilles.gif');
INSERT INTO `countries` VALUES (72, 'Ukraine', 'ukraine.gif');
INSERT INTO `countries` VALUES (73, 'Venezuela', 'venezuela.gif');
INSERT INTO `countries` VALUES (74, 'Hungary', 'hungary.gif');
INSERT INTO `countries` VALUES (75, 'Romania', 'romania.gif');
INSERT INTO `countries` VALUES (76, 'Vanuatu', 'vanuatu.gif');
INSERT INTO `countries` VALUES (77, 'Vietnam', 'vietnam.gif');
INSERT INTO `countries` VALUES (78, 'Trinidad & Tobago', 'trinidadandtobago.gif');
INSERT INTO `countries` VALUES (79, 'Honduras', 'honduras.gif');
INSERT INTO `countries` VALUES (80, 'Kyrgyzstan', 'kyrgyzstan.gif');
INSERT INTO `countries` VALUES (81, 'Ecuador', 'ecuador.gif');
INSERT INTO `countries` VALUES (82, 'Bahamas', 'bahamas.gif');
INSERT INTO `countries` VALUES (83, 'Peru', 'peru.gif');
INSERT INTO `countries` VALUES (84, 'Cambodia', 'cambodia.gif');
INSERT INTO `countries` VALUES (85, 'Barbados', 'barbados.gif');
INSERT INTO `countries` VALUES (86, 'Bangladesh', 'bangladesh.gif');
INSERT INTO `countries` VALUES (87, 'Laos', 'laos.gif');
INSERT INTO `countries` VALUES (88, 'Uruguay', 'uruguay.gif');
INSERT INTO `countries` VALUES (89, 'Antigua Barbuda', 'antiguabarbuda.gif');
INSERT INTO `countries` VALUES (90, 'Paraguay', 'paraguay.gif');
INSERT INTO `countries` VALUES (93, 'Thailand', 'thailand.gif');
INSERT INTO `countries` VALUES (92, 'Union of Soviet Socialist Republics', 'ussr.gif');
INSERT INTO `countries` VALUES (94, 'Senegal', 'senegal.gif');
INSERT INTO `countries` VALUES (95, 'Togo', 'togo.gif');
INSERT INTO `countries` VALUES (96, 'North Korea', 'northkorea.gif');
INSERT INTO `countries` VALUES (97, 'Croatia', 'croatia.gif');
INSERT INTO `countries` VALUES (98, 'Estonia', 'estonia.gif');
INSERT INTO `countries` VALUES (99, 'Colombia', 'colombia.gif');
INSERT INTO `countries` VALUES (100, 'Lebanon', 'lebanon.gif');
INSERT INTO `countries` VALUES (101, 'Latvia', 'latvia.gif');
INSERT INTO `countries` VALUES (102, 'Costa Rica', 'costarica.gif');
INSERT INTO `countries` VALUES (103, 'Egypt', 'egypt.gif');
INSERT INTO `countries` VALUES (104, 'Bulgaria', 'bulgaria.gif');

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `files`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `forums`
-- 

DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `minclassread` tinyint(3) unsigned NOT NULL default '0',
  `minclasswrite` tinyint(3) unsigned NOT NULL default '0',
  `postcount` int(10) unsigned NOT NULL default '0',
  `topiccount` int(10) unsigned NOT NULL default '0',
  `minclasscreate` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `forums`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `friends`
-- 

DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `friends`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `messages`
-- 

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `msg` text,
  `unread` enum('yes','no') NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL default '0',
  `location` enum('in','out','both') NOT NULL default 'in',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `messages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `news`
-- 

INSERT INTO `news` VALUES (1, 1, '2006-02-22 16:19:29', 'error_reporting(E_ALL);\r\n\r\nset_error_handler(''tbdev_error_handler'');\r\n\r\nfunction tbdev_error_handler($errno, $error, $file, $line) {\r\n	$message = "[ERROR][$errno][$error][$file:$line]<br />";\r\n	print "$message";\r\n	}\r\nfunction getmicrotime( )\r\n{\r\n        list( $usec, $sec ) = explode( " ", microtime( ) );\r\n\r\n        return ( (float)$usec + (float)$sec );\r\n}\r\n\r\n$generated = getmicrotime( );\r\n	\r\nfunction local_user()\r\n{\r\n  return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];\r\n}\r\n//$FUNDS = "$2,610.31";\r\n\r\n$SITE_ONLINE = true;\r\n//$SITE_ONLINE = local_user();\r\n//$SITE_ONLINE = false;\r\n\r\n$max_torrent_size = 1000000;\r\n$announce_interval = 60 * 30;\r\n$signup_timeout = 86400 * 3;\r\n$minvotes = 1;\r\n$max_dead_torrent_time = 6 * 3600;\r\n\r\n// Max users on site\r\n$maxusers = 75000; // LoL Who we kiddin'' here?\r\n\r\n// Max users on site\r\n$maxusers = 5000;\r\n\r\n// ONLY USE ONE OF THE FOLLOWING DEPENDING ON YOUR O/S!!!\r\n//$torrent_dir = "/var/tb/torrents";    # FOR UNIX ONLY - must be writable for httpd user\r\n$torrent_dir = "C:\\www\\apache\\Apache2\\htdocs\\TBDEV\\torrents";    # FOR WINDOWS ONLY - must be writable for httpd user\r\n\r\n# the first one will be displayed on the pages\r\n$announce_urls = array();\r\n$announce_urls[] = "http://localhost/TBDEV/announce.php";\r\n$announce_urls[] = "http://domain.com:82/announce.php";\r\n$announce_urls[] = "http://domain.com:83/announce.php";\r\n\r\n//$BASEURL = "http://" . $_SERVER[''HTTP_HOST''];\r\n                     //. rtrim(dirname($_SERVER[''PHP_SELF'']), ''/\\\\'');\r\n                     //. "/" . $relative_url);\r\n// Set this to your site URL... No ending slash!\r\ndefine("P_SERVER", "http://" . $_SERVER[''HTTP_HOST'']);\r\n// Set this to your site URL... No ending slash!\r\ndefine("P_ROOT", "/TBDEV/");\r\ndefine("P_MAIN", P_SERVER.P_ROOT);\r\n$DEFAULTBASEURL = "http://localhost/TBDEV";\r\n\r\n//set this to true to make this a tracker that only registered users may use\r\n$MEMBERSONLY = true;\r\n\r\n//maximum number of peers (seeders+leechers) allowed before torrents starts to be deleted to make room...\r\n//set this to something high if you don''t require this feature\r\n$PEERLIMIT = 50000;\r\n\r\n// Email for sender/return path.\r\n$SITEEMAIL = "noreply@domain.com";\r\n\r\n$SITENAME = "TBDEV.NET";\r\n\r\n$autoclean_interval = 900;\r\n$pic_base_url = "/pic/";\r\n$ss_uri = "default.css";\r\n');

-- --------------------------------------------------------

-- 
-- Table structure for table `peers`
-- 

DROP TABLE IF EXISTS `peers`;
CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `peer_id` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',
  `ip` varchar(64) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `started` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) NOT NULL default '',
  `finishedat` int(10) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `peers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pollanswers`
-- 

DROP TABLE IF EXISTS `pollanswers`;
CREATE TABLE `pollanswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `pollanswers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `polls`
-- 

DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` varchar(255) NOT NULL default '',
  `option0` varchar(40) NOT NULL default '',
  `option1` varchar(40) NOT NULL default '',
  `option2` varchar(40) NOT NULL default '',
  `option3` varchar(40) NOT NULL default '',
  `option4` varchar(40) NOT NULL default '',
  `option5` varchar(40) NOT NULL default '',
  `option6` varchar(40) NOT NULL default '',
  `option7` varchar(40) NOT NULL default '',
  `option8` varchar(40) NOT NULL default '',
  `option9` varchar(40) NOT NULL default '',
  `option10` varchar(40) NOT NULL default '',
  `option11` varchar(40) NOT NULL default '',
  `option12` varchar(40) NOT NULL default '',
  `option13` varchar(40) NOT NULL default '',
  `option14` varchar(40) NOT NULL default '',
  `option15` varchar(40) NOT NULL default '',
  `option16` varchar(40) NOT NULL default '',
  `option17` varchar(40) NOT NULL default '',
  `option18` varchar(40) NOT NULL default '',
  `option19` varchar(40) NOT NULL default '',
  `sort` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `polls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `body` text,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `posts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `readposts`
-- 

DROP TABLE IF EXISTS `readposts`;
CREATE TABLE `readposts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  `lastpostread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `readposts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `sitelog`
-- 

DROP TABLE IF EXISTS `sitelog`;
CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `sitelog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `stylesheets`
-- 

DROP TABLE IF EXISTS `stylesheets`;
CREATE TABLE `stylesheets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `stylesheets`
-- 

INSERT INTO `stylesheets` VALUES (1, 'default.css', '(default)');
INSERT INTO `stylesheets` VALUES (2, 'large.css', 'Large text');

-- --------------------------------------------------------

-- 
-- Table structure for table `topics`
-- 

DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(40) default NULL,
  `locked` enum('yes','no') NOT NULL default 'no',
  `forumid` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') NOT NULL default 'no',
  `views` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `subject` (`subject`),
  KEY `lastpost` (`lastpost`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `topics`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `torrents`
-- 

DROP TABLE IF EXISTS `torrents`;
CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `save_as` varchar(255) NOT NULL default '',
  `search_text` text NOT NULL,
  `descr` text NOT NULL,
  `ori_descr` text NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') NOT NULL default 'yes',
  `banned` enum('yes','no') NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `torrents`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) NOT NULL default '',
  `old_password` varchar(40) NOT NULL default '',
  `passhash` varchar(32) NOT NULL default '',
  `secret` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `status` enum('pending','confirmed') NOT NULL default 'pending',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `editsecret` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',
  `privacy` enum('strong','normal','low') NOT NULL default 'normal',
  `stylesheet` int(10) default '1',
  `info` text,
  `acceptpms` enum('yes','friends','no') NOT NULL default 'yes',
  `ip` varchar(15) NOT NULL default '',
  `class` tinyint(3) unsigned NOT NULL default '0',
  `avatar` varchar(100) NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(30) NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(100) NOT NULL default '',
  `modcomment` text NOT NULL,
  `enabled` enum('yes','no') NOT NULL default 'yes',
  `avatars` enum('yes','no') NOT NULL default 'yes',
  `donor` enum('yes','no') NOT NULL default 'no',
  `warned` enum('yes','no') NOT NULL default 'no',
  `warneduntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') NOT NULL default 'yes',
  `savepms` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 'CoLdFuSiOn', '', 'd97d3bff35c5d98250f44cc7ec69640e', '³àƒÆxÿ(.›Š²öÞ1P\n˜iØ', 'bob@bob.com', 'confirmed', '2006-02-21 02:35:10', '2006-02-22 08:29:40', '2006-02-22 16:30:31', '', 'normal', 1, NULL, 'yes', '127.0.0.1', 6, '', 0, 0, '', 0, '', '', 'yes', 'yes', 'no', 'no', '0000-00-00 00:00:00', 0, 0, 0, 'yes', 'no');
