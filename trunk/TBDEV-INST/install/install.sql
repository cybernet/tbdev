-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 12, 2009 at 09:23 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.1
-- 
-- 


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

-- 
-- Table structure for table `addedrequests`
-- 

CREATE TABLE `addedrequests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `requestid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`id`),
  KEY `userid` (`userid`),
  KEY `requestid_userid` (`requestid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `addedrequests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `announcement_main`
-- 

CREATE TABLE `announcement_main` (
  `main_id` int(10) unsigned NOT NULL auto_increment,
  `owner_id` int(10) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `sql_query` text NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`main_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `announcement_main`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `announcement_process`
-- 

CREATE TABLE `announcement_process` (
  `process_id` int(10) unsigned NOT NULL auto_increment,
  `main_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`process_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `announcement_process`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `attachmentdownloads`
-- 

CREATE TABLE `attachmentdownloads` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fileid` int(10) NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `userid` int(10) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `downloads` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fileid_userid` (`fileid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `attachmentdownloads`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `attachments`
-- 

CREATE TABLE `attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `postid` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  `owner` int(10) unsigned NOT NULL default '0',
  `downloads` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `postid` (`postid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `attachments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `avps`
-- 

CREATE TABLE `avps` (
  `arg` varchar(20) collate utf8_bin NOT NULL default '',
  `value_s` text collate utf8_bin NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  `value_d` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `avps`
-- 

INSERT INTO `avps` VALUES ('lastcleantime', '', 0, 0, '0000-00-00 00:00:00');
INSERT INTO `avps` VALUES ('last24', '0', 0, 0, '0000-00-00 00:00:00');
INSERT INTO `avps` VALUES ('bestfilmofweek', '0', 0, 0, '0000-00-00 00:00:00');
INSERT INTO `avps` VALUES ('inactivemail', '0', 0, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `bannedemails`
-- 

CREATE TABLE `bannedemails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `bannedemails`
-- 

INSERT INTO `bannedemails` VALUES (159, '2007-06-19 23:37:08', 1, 'Fake provider', '*@emailias.com');
INSERT INTO `bannedemails` VALUES (158, '2007-06-19 23:37:08', 1, 'Fake provider', '*@e4ward.com');
INSERT INTO `bannedemails` VALUES (157, '2007-06-19 23:37:08', 1, 'Fake provider', '*@dumpmail.de');
INSERT INTO `bannedemails` VALUES (156, '2007-06-19 23:37:08', 1, 'Fake provider', '*@dontreg.com');
INSERT INTO `bannedemails` VALUES (155, '2007-06-19 23:37:08', 1, 'Fake provider', '*@disposeamail.com');
INSERT INTO `bannedemails` VALUES (154, '2007-06-19 23:37:08', 1, 'Fake provider', '*@antispam24.de');
INSERT INTO `bannedemails` VALUES (153, '2007-06-19 23:37:08', 1, 'Fake provider', '*@trash-mail.de');
INSERT INTO `bannedemails` VALUES (152, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spambog.de');
INSERT INTO `bannedemails` VALUES (151, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spambog.com');
INSERT INTO `bannedemails` VALUES (150, '2007-06-19 23:37:08', 1, 'Fake provider', '*@discardmail.com');
INSERT INTO `bannedemails` VALUES (149, '2007-06-19 23:37:08', 1, 'Fake provider', '*@discardmail.de');
INSERT INTO `bannedemails` VALUES (148, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailinator.com');
INSERT INTO `bannedemails` VALUES (147, '2007-06-19 23:37:08', 1, 'Fake provider', '*@wuzup.net');
INSERT INTO `bannedemails` VALUES (146, '2007-06-19 23:37:08', 1, 'Fake provider', '*@junkmail.com');
INSERT INTO `bannedemails` VALUES (145, '2007-06-19 23:37:08', 1, 'Fake provider', '*@clarkgriswald.net');
INSERT INTO `bannedemails` VALUES (144, '2007-06-19 23:37:08', 1, 'Fake provider', '*@2prong.com');
INSERT INTO `bannedemails` VALUES (143, '2007-06-19 23:37:08', 1, 'Fake provider', '*@jrwilcox.com');
INSERT INTO `bannedemails` VALUES (142, '2007-06-19 23:37:08', 1, 'Fake provider', '*@10minutemail.com');
INSERT INTO `bannedemails` VALUES (141, '2007-06-19 23:37:08', 1, 'Fake provider', '*@pookmail.com');
INSERT INTO `bannedemails` VALUES (140, '2007-06-19 23:37:08', 1, 'Fake provider', '*@golfilla.info');
INSERT INTO `bannedemails` VALUES (139, '2007-06-19 23:37:08', 1, 'Fake provider', '*@afrobacon.com');
INSERT INTO `bannedemails` VALUES (138, '2007-06-19 23:37:08', 1, 'Fake provider', '*@senseless-entertainment.com');
INSERT INTO `bannedemails` VALUES (137, '2007-06-19 23:37:08', 1, 'Fake provider', '*@put2.net');
INSERT INTO `bannedemails` VALUES (136, '2007-06-19 23:37:08', 1, 'Fake provider', '*@temporaryinbox.com');
INSERT INTO `bannedemails` VALUES (135, '2007-06-19 23:37:08', 1, 'Fake provider', '*@slaskpost.se');
INSERT INTO `bannedemails` VALUES (161, '2007-06-19 23:37:08', 1, 'Fake provider', '*@haltospam.com');
INSERT INTO `bannedemails` VALUES (162, '2007-06-19 23:37:08', 1, 'Fake provider', '*@h8s.org');
INSERT INTO `bannedemails` VALUES (163, '2007-06-19 23:37:08', 1, 'Fake provider', '*@ipoo.org');
INSERT INTO `bannedemails` VALUES (164, '2007-06-19 23:37:08', 1, 'Fake provider', '*@oopi.org');
INSERT INTO `bannedemails` VALUES (165, '2007-06-19 23:37:08', 1, 'Fake provider', '*@poofy.org');
INSERT INTO `bannedemails` VALUES (166, '2007-06-19 23:37:08', 1, 'Fake provider', '*@jetable.org');
INSERT INTO `bannedemails` VALUES (167, '2007-06-19 23:37:08', 1, 'Fake provider', '*@kasmail.com');
INSERT INTO `bannedemails` VALUES (168, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mail-filter.com');
INSERT INTO `bannedemails` VALUES (169, '2007-06-19 23:37:08', 1, 'Fake provider', '*@maileater.com');
INSERT INTO `bannedemails` VALUES (170, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailexpire.com');
INSERT INTO `bannedemails` VALUES (171, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailnull.com');
INSERT INTO `bannedemails` VALUES (172, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailshell.com');
INSERT INTO `bannedemails` VALUES (173, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mymailoasis.com');
INSERT INTO `bannedemails` VALUES (174, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mytrashmail.com');
INSERT INTO `bannedemails` VALUES (175, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mytrashmail.net');
INSERT INTO `bannedemails` VALUES (176, '2007-06-19 23:37:08', 1, 'Fake provider', '*@shortmail.net');
INSERT INTO `bannedemails` VALUES (177, '2007-06-19 23:37:08', 1, 'Fake provider', '*@sneakemail.com');
INSERT INTO `bannedemails` VALUES (178, '2007-06-19 23:37:08', 1, 'Fake provider', '*@sofort-mail.de');
INSERT INTO `bannedemails` VALUES (179, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamcon.org');
INSERT INTO `bannedemails` VALUES (180, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamday.com');
INSERT INTO `bannedemails` VALUES (181, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamex.com');
INSERT INTO `bannedemails` VALUES (182, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamgourmet.com');
INSERT INTO `bannedemails` VALUES (183, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamhole.com');
INSERT INTO `bannedemails` VALUES (184, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spammotel.com');
INSERT INTO `bannedemails` VALUES (185, '2007-06-19 23:37:08', 1, 'Fake provider', '*@tempemail.net');
INSERT INTO `bannedemails` VALUES (186, '2007-06-19 23:37:08', 1, 'Fake provider', '*@tempinbox.com');
INSERT INTO `bannedemails` VALUES (187, '2007-06-19 23:37:08', 1, 'Fake provider', '*@throwaway.de');
INSERT INTO `bannedemails` VALUES (188, '2007-06-19 23:37:08', 1, 'Fake provider', '*@woodyland.org');
INSERT INTO `bannedemails` VALUES (189, '2007-06-19 23:37:08', 1, 'Fake provider', '*@iximail.com');
INSERT INTO `bannedemails` VALUES (190, '2007-06-19 23:37:08', 1, 'Fake provider', '*@iheartspam.org');
INSERT INTO `bannedemails` VALUES (191, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spaml.com');
INSERT INTO `bannedemails` VALUES (192, '2007-06-19 23:37:08', 1, 'Fake provider', '*@noclickemail.com');
INSERT INTO `bannedemails` VALUES (193, '2007-06-19 23:37:08', 1, 'Fake provider', '*@0clickemail.com');
INSERT INTO `bannedemails` VALUES (194, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmai.com');
INSERT INTO `bannedemails` VALUES (195, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hitmail.com');
INSERT INTO `bannedemails` VALUES (196, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hoitmail.com');
INSERT INTO `bannedemails` VALUES (197, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@homtail.com');
INSERT INTO `bannedemails` VALUES (198, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmail.se');
INSERT INTO `bannedemails` VALUES (199, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmal.com');
INSERT INTO `bannedemails` VALUES (200, '2007-06-19 23:37:08', 1, 'Misspelled provider?', '*@bredbnd.net');
INSERT INTO `bannedemails` VALUES (201, '2008-09-20 20:02:14', 1, 'Spoof Hotmail', '@hiotmail.com');
INSERT INTO `bannedemails` VALUES (202, '2008-12-06 15:43:07', 1, 'fake', '*@free1houremail.com');

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) collate utf8_bin NOT NULL default '',
  `first` int(11) default NULL,
  `last` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `bans`
-- 

INSERT INTO `bans` VALUES (3, '2008-08-16 08:38:37', 3, 'test', 1684300900, 1684300900);
INSERT INTO `bans` VALUES (4, '2008-08-30 16:38:37', 1, 'test', 1684300902, 1684300903);

-- --------------------------------------------------------

-- 
-- Table structure for table `blackjack`
-- 

CREATE TABLE `blackjack` (
  `userid` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `status` enum('playing','waiting') NOT NULL default 'playing',
  `cards` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `subject` varchar(30) NOT NULL default 'BlackJack Results',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `blackjack`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `blocks`
-- 

CREATE TABLE `blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `blocks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `bonus`
-- 

CREATE TABLE `bonus` (
  `id` int(5) NOT NULL auto_increment,
  `enabled` enum('yes','no') NOT NULL default 'yes' COMMENT 'This will determined a switch if the bonus is enabled or not! enabled by default',
  `bonusname` varchar(50) NOT NULL default '',
  `points` decimal(10,1) NOT NULL default '0.0',
  `description` text NOT NULL,
  `art` varchar(10) NOT NULL default 'traffic',
  `menge` bigint(20) unsigned NOT NULL default '0',
  `pointspool` decimal(10,1) NOT NULL default '0.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `bonus`
-- 

INSERT INTO `bonus` VALUES (1, 'yes', '1.0GB Uploaded', 275.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1073741824, 0.0);
INSERT INTO `bonus` VALUES (2, 'yes', '2.5GB Uploaded', 350.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 2684354560, 0.0);
INSERT INTO `bonus` VALUES (3, 'yes', '5GB Uploaded', 550.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 5368709120, 0.0);
INSERT INTO `bonus` VALUES (4, 'yes', '3 Invites', 1000.0, 'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.', 'invite', 3, 0.0);
INSERT INTO `bonus` VALUES (5, 'yes', 'Custom Title!', 500.0, 'For only 500 Bonus Points, you can buy yourself a custom title. The only restrictions are no foul or offensive language or user class can be entered. The points are then removed from your Bonus Bank and your special title is changed to the title of your choice', 'title', 1, 0.0);
INSERT INTO `bonus` VALUES (6, 'yes', 'VIP Status', 5000.0, 'With enough bonus points acquired, you can buy yourself VIP status for one month. The points are then removed from your Bonus Bank and your status is changed.', 'class', 1, 0.0);
INSERT INTO `bonus` VALUES (7, 'yes', 'Give A Karma Gift', 100.0, 'Well perhaps you don''t need the upload credit, but you know somebody that could use the Karma boost! You are now able to give your Karma credits as  a gift! The points are then removed from your Bonus Bank and  added to the account of a user of your choice!\r\n\r\nAnd they recieve a PM with all the info as well as who it came from...', 'gift_1', 1073741824, 0.0);
INSERT INTO `bonus` VALUES (8, 'yes', 'Custom Smilies', 300.0, 'With enough bonus points acquired, you can buy yourself a set of custom smilies for one month! The points are then removed from your Bonus Bank and with a click of a link, your new smilies are available whenever you post or comment!', 'smile', 1, 0.0);
INSERT INTO `bonus` VALUES (9, 'yes', 'Remove Warning', 1000.0, 'With enough bonus points acquired... So you''ve been naughty... tsk tsk.. Yep now for only 1000 points you can have that warning taken away !', 'warning', 1, 0.0);
INSERT INTO `bonus` VALUES (10, 'yes', 'Ratio Fix', 500.0, 'With enough bonus points acquired, you can bring the ratio of one torrent to a 1 to 1 ratio! The points are then removed from your Bonus Bank and your status is changed.', 'ratio', 1, 0.0);
INSERT INTO `bonus` VALUES (11, 'yes', '3 Freeleech Slots', 1000.0, 'With enough bonus points acquired, you are able to exchange them for some Freeleech Slots. The points are then removed from your Bonus Bank and the slots are added to your free slots amount.', 'freeslots', 3, 0.0);
INSERT INTO `bonus` VALUES (12, 'yes', '10GB Uploaded', 1000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 10737418240, 0.0);
INSERT INTO `bonus` VALUES (13, 'yes', '25GB Uploaded', 2000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 26843545600, 0.0);
INSERT INTO `bonus` VALUES (14, 'yes', '50GB Uploaded', 4000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 53687091200, 0.0);
INSERT INTO `bonus` VALUES (15, 'yes', '100GB Uploaded', 8000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 107374182400, 0.0);
INSERT INTO `bonus` VALUES (16, 'yes', '520GB Uploaded', 40000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 558345748480, 0.0);
INSERT INTO `bonus` VALUES (17, 'yes', '1TB Uploaded', 80000.0, 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1099511627776, 0.0);
INSERT INTO `bonus` VALUES (18, 'yes', '200 Bonus Points - Invite trade-in', 1.0, 'If you have 1 invite and don''t use them click the button to trade them in for 200 Bonus Points.', 'itrade', 200, 0.0);
INSERT INTO `bonus` VALUES (19, 'yes', 'Freeslots - Invite trade-in', 1.0, 'If you have 1 invite and don''t use them click the button to trade them in for 2 Free Slots.', 'itrade2', 2, 0.0);

-- --------------------------------------------------------

-- 
-- Table structure for table `bookmarks`
-- 

CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `private` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  KEY `torrent_user_idx` (`torrentid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `bookmarks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cards`
-- 

CREATE TABLE `cards` (
  `id` int(11) NOT NULL auto_increment,
  `points` int(11) NOT NULL default '0',
  `pic` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `cards`
-- 

INSERT INTO `cards` VALUES (1, 2, '2p.bmp');
INSERT INTO `cards` VALUES (2, 3, '3p.bmp');
INSERT INTO `cards` VALUES (3, 4, '4p.bmp');
INSERT INTO `cards` VALUES (4, 5, '5p.bmp');
INSERT INTO `cards` VALUES (5, 6, '6p.bmp');
INSERT INTO `cards` VALUES (6, 7, '7p.bmp');
INSERT INTO `cards` VALUES (7, 8, '8p.bmp');
INSERT INTO `cards` VALUES (8, 9, '9p.bmp');
INSERT INTO `cards` VALUES (9, 10, '10p.bmp');
INSERT INTO `cards` VALUES (10, 10, 'vp.bmp');
INSERT INTO `cards` VALUES (11, 10, 'dp.bmp');
INSERT INTO `cards` VALUES (12, 10, 'kp.bmp');
INSERT INTO `cards` VALUES (13, 1, 'tp.bmp');
INSERT INTO `cards` VALUES (14, 2, '2b.bmp');
INSERT INTO `cards` VALUES (15, 3, '3b.bmp');
INSERT INTO `cards` VALUES (16, 4, '4b.bmp');
INSERT INTO `cards` VALUES (17, 5, '5b.bmp');
INSERT INTO `cards` VALUES (18, 6, '6b.bmp');
INSERT INTO `cards` VALUES (19, 7, '7b.bmp');
INSERT INTO `cards` VALUES (20, 8, '8b.bmp');
INSERT INTO `cards` VALUES (21, 9, '9b.bmp');
INSERT INTO `cards` VALUES (22, 10, '10b.bmp');
INSERT INTO `cards` VALUES (23, 10, 'vb.bmp');
INSERT INTO `cards` VALUES (24, 10, 'db.bmp');
INSERT INTO `cards` VALUES (25, 10, 'kb.bmp');
INSERT INTO `cards` VALUES (26, 1, 'tb.bmp');
INSERT INTO `cards` VALUES (27, 2, '2k.bmp');
INSERT INTO `cards` VALUES (28, 3, '3k.bmp');
INSERT INTO `cards` VALUES (29, 4, '4k.bmp');
INSERT INTO `cards` VALUES (30, 5, '5k.bmp');
INSERT INTO `cards` VALUES (31, 6, '6k.bmp');
INSERT INTO `cards` VALUES (32, 7, '7k.bmp');
INSERT INTO `cards` VALUES (33, 8, '8k.bmp');
INSERT INTO `cards` VALUES (34, 9, '9k.bmp');
INSERT INTO `cards` VALUES (35, 10, '10k.bmp');
INSERT INTO `cards` VALUES (36, 10, 'vk.bmp');
INSERT INTO `cards` VALUES (37, 10, 'dk.bmp');
INSERT INTO `cards` VALUES (38, 10, 'kk.bmp');
INSERT INTO `cards` VALUES (39, 1, 'tk.bmp');
INSERT INTO `cards` VALUES (40, 2, '2c.bmp');
INSERT INTO `cards` VALUES (41, 3, '3c.bmp');
INSERT INTO `cards` VALUES (42, 4, '4c.bmp');
INSERT INTO `cards` VALUES (43, 5, '5c.bmp');
INSERT INTO `cards` VALUES (44, 6, '6c.bmp');
INSERT INTO `cards` VALUES (45, 7, '7c.bmp');
INSERT INTO `cards` VALUES (46, 8, '8c.bmp');
INSERT INTO `cards` VALUES (47, 9, '9c.bmp');
INSERT INTO `cards` VALUES (48, 10, '10c.bmp');
INSERT INTO `cards` VALUES (49, 10, 'vc.bmp');
INSERT INTO `cards` VALUES (50, 10, 'dc.bmp');
INSERT INTO `cards` VALUES (51, 10, 'kc.bmp');
INSERT INTO `cards` VALUES (52, 1, 'tc.bmp');

-- --------------------------------------------------------

-- 
-- Table structure for table `casino`
-- 

CREATE TABLE `casino` (
  `userid` int(10) NOT NULL default '0',
  `win` bigint(20) default NULL,
  `lost` bigint(20) default NULL,
  `trys` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `enableplay` enum('yes','no') collate latin1_general_ci NOT NULL default 'yes',
  `deposit` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `casino`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `casino_bets`
-- 

CREATE TABLE `casino_bets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `proposed` varchar(40) collate latin1_general_ci NOT NULL default '',
  `challenged` varchar(40) collate latin1_general_ci NOT NULL default '',
  `amount` bigint(20) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`proposed`,`challenged`,`amount`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- 
-- Dumping data for table `casino_bets`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) collate utf8_bin NOT NULL default '',
  `image` varchar(255) collate utf8_bin NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` VALUES (1, 'Appz/PC ISO', 'cat_apps.gif');
INSERT INTO `categories` VALUES (2, 'Games/PC ISO', 'cat_games.gif');
INSERT INTO `categories` VALUES (3, 'Movies/SVCD', 'cat_svcd.gif');
INSERT INTO `categories` VALUES (4, 'Music', 'cat_music.gif');
INSERT INTO `categories` VALUES (5, 'Episodes', 'cat_episodes.gif');
INSERT INTO `categories` VALUES (6, 'XXX', 'cat_xxx.gif');
INSERT INTO `categories` VALUES (7, 'Games/GBA', 'cat_games.gif');
INSERT INTO `categories` VALUES (8, 'Games/PS2', 'cat_games.gif');
INSERT INTO `categories` VALUES (9, 'Anime', 'cat_anime.gif');
INSERT INTO `categories` VALUES (10, 'Movies/XviD', 'cat_xvid.gif');
INSERT INTO `categories` VALUES (11, 'Movies/DVD-R', 'cat_dvdr.gif');
INSERT INTO `categories` VALUES (12, 'Games/PC Rips', 'cat_games.gif');
INSERT INTO `categories` VALUES (13, 'Appz/misc', 'cat_apps.gif');

-- --------------------------------------------------------

-- 
-- Table structure for table `changelog`
-- 

CREATE TABLE `changelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `sticky` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `changelog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cheaters`
-- 

CREATE TABLE `cheaters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `client` varchar(255) NOT NULL default '',
  `rate` varchar(255) NOT NULL default '',
  `beforeup` varchar(255) NOT NULL default '',
  `upthis` varchar(255) NOT NULL default '',
  `timediff` varchar(255) NOT NULL default '',
  `userip` varchar(15) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `cheaters`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `coins`
-- 

CREATE TABLE `coins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `points` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `coins`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text collate utf8_bin NOT NULL,
  `ori_text` text collate utf8_bin NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `request` int(11) NOT NULL default '0',
  `offer` int(11) NOT NULL default '0',
  `anonymous` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `photo_gallery` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `name` varchar(255) collate utf8_bin NOT NULL default '',
  `value` varchar(255) collate utf8_bin NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `config`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_bin default NULL,
  `flagpic` varchar(50) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
INSERT INTO `countries` VALUES (27, 'India', 'india.gif');
INSERT INTO `countries` VALUES (28, 'Albania', 'albania.gif');
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
INSERT INTO `countries` VALUES (70, 'Macedonia', 'macedonia.gif');
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
INSERT INTO `countries` VALUES (105, 'Isla de Muerte', 'jollyroger.gif');
INSERT INTO `countries` VALUES (106, 'Scotland', 'scotland.gif');

-- --------------------------------------------------------

-- 
-- Table structure for table `delete_hr`
-- 

CREATE TABLE `delete_hr` (
  `delete_date` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `delete_hr`
-- 

INSERT INTO `delete_hr` VALUES ('2008-09-25 20:31:36');

-- --------------------------------------------------------

-- 
-- Table structure for table `dox`
-- 

CREATE TABLE `dox` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `size` int(10) unsigned NOT NULL default '0',
  `uppedby` int(10) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `hits` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `dox`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `faq`
-- 

CREATE TABLE `faq` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(3) unsigned NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `answer` text NOT NULL,
  `ctime` int(11) unsigned NOT NULL default '0',
  `mtime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat_id` (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `faq`
-- 
INSERT INTO `faq` VALUES (1, 1, 'What is this bittorrent all about anyway? How do I get the files?', 'Check out Brian''s BitTorrent FAQ and Guide.', 1162676179, 1162844691);
INSERT INTO `faq` VALUES (2, 2, 'Where does the donated money go?', 'yoursite is situated on a dedicated server in the Netherlands. For the moment we have monthly running costs of approximately ', 1162868062, 1162406569);
INSERT INTO `faq` VALUES (3, 1, 'Where can I get a copy of the source code?', 'Its available @ http://tbdev.net ...Its open  free source code..... Made by the People For The People...\r\n\r\nI think it''s fair to mention some of the coders that this site is now based on, both directly and indirectly. the Bit Torrent community is a friendly place for coders, and I''d like to say thanks here for all their hard work and support.\r\n\r\nI''m sure I''ve forgotten a few, but we all owe a debt to:\r\nCoLdFuSiOn, Laffin, Retro,  Sir_Snugglebunny, System, DRRRRRR, dokty, traffic,  Bleach, Devinkray, MisterB, EnzoF1, Wilba, Rightthere, S4ne, RAW, DemoN, Oink, Psor, Bodhisattva, Echo, TheBrass, Tux2005, x0r, pdq, Bigjoos, BIGBOSS, TheMask, Lamers, Lords, Sparks, Cddvdheaven, TVRecall, Cue, putyn, neptune, Alex2005.', 1162676179, 1232298251);
INSERT INTO `faq` VALUES (4, 2, 'I registered an account but did not receive the confirmation e-mail!', 'You can use this form to delete the account so you can re-register. Note though that if you didn''t receive the email the first time it will probably not succeed the second time either so you should really try another email address.', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (5, 2, 'I have lost my user name or password! Can you send it to me?', 'Please use this form to have the login details mailed back to you.', 1162868062, 1162406569);
INSERT INTO `faq` VALUES (6, 2, 'Can you rename my account?', 'We do not rename accounts. Please create a new one. (Use this form to delete your present account.)', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (7, 2, 'Can you delete my (confirmed) account?', 'You can do it yourself by using this form', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (8, 2, 'So, what''s MY ratio?', 'Click on your profile, then on your user name (at the top).\r\n\r\nIt''s important to distinguish between your overall ratio and the individual ratio on each torrent you may be seeding or leeching. The overall ratio takes into account the total uploaded and downloaded from your account since you joined the site. The individual ratio takes into account those values for each torrent.\r\n\r\nYou may see two symbols instead of a number: "Inf.", which is just an abbreviation for Infinity, and means that you have downloaded 0 bytes while uploading a non-zero amount (ul/dl becomes infinity); "---", which should be read as "non-available", and shows up when you have both downloaded and uploaded 0 bytes (ul/dl = 0/0 which is an indeterminate amount).', 1162868062, 1162651503);
INSERT INTO `faq` VALUES (9, 2, 'Why is my IP displayed on my details page?', 'Only you and the site moderators can view your IP address and email. Regular users do not see that information.', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (10, 2, 'Help! I cannot login!? (a.k.a. Login of Death)', 'This problem sometimes occurs with MSIE. Close all Internet Explorer windows and open Internet Options in the control panel. Click the Delete Cookies button. You should now be able to login.', 1162868062, 1162406569);
INSERT INTO `faq` VALUES (11, 2, 'My IP address is dynamic. How do I stay logged in?', 'You do not have to anymore. All you have to do is make sure you are logged in with your actual IP when starting a torrent session. After that, even if the IP changes mid-session, the seeding or leeching will continue and the statistics will update without any problem.', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (12, 2, 'Why am I listed as not connectable? (And why should I care?)', 'The tracker has determined that you are firewalled or NATed and cannot accept incoming connections.\r\n\r\nThis means that other peers in the swarm will be unable to connect to you, only you to them. Even worse, if two peers are both in this state they will not be able to connect at all. This has obviously a detrimental effect on the overall speed.\r\n\r\nThe way to solve the problem involves opening the ports used for incoming connections (the same range you defined in your client) on the firewall and/or configuring your NAT server to use a basic form of NAT for that range instead of NAPT (the actual process differs widely between different router models. Check your router documentation and/or support forum. You will also find lots of information on the subject at PortForward).', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (13, 2, 'What are the different user classes?', 'User The default class of new members.\r\n Power User Can download DOX over 1MB and view NFO files.\r\n Star Has donated money to TorrentBits.org .\r\n VIP Same privileges as Power User and is considered an Elite Member of TorrentBits. Immune to automatic demotion.\r\n Other Customised title.\r\n Uploader Same as PU except with upload rights and immune to automatic demotion.\r\n Moderator Can edit and delete any uploaded torrents. Can also moderate user comments and disable accounts.\r\n Administrator Can do just about anything.\r\n SysOp Redbeard (site owner).', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (14, 2, 'How does this promotion thing work anyway?', 'Power User Must have been be a member for at least 4 weeks, have uploaded at least 25GB and have a ratio at or above 1.05.\r\nThe promotion is automatic when these conditions are met. Note that you will be automatically demoted from\r\nthis status if your ratio drops below 0.95 at any time.\r\n Star Just donate, and send Redbeard - and only Redbeard - the details.\r\n VIP Assigned by mods at their discretion to users they feel contribute something special to the site.\r\n(Anyone begging for VIP status will be automatically disqualified.)\r\n Other Conferred by mods at their discretion (not available to Users or Power Users).\r\n Uploader Appointed by Admins/SysOp (see the ''Uploading'' section for conditions).\r\n Moderator You don''t ask us, we''ll ask you!', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (15, 2, 'Hey! I''ve seen Power Users with less than 25GB uploaded!', 'The PU limit used to be 10GB and we didn''t demote anyone when we raised it to 25GB', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (16, 2, 'Why can''t my friend become a member?', 'There is a 75.000 users limit. When that number is reached we stop accepting new members. Accounts inactive for more than 42 days are automatically deleted, so keep trying. (There is no reservation or queuing system, don''t ask for that.)', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (17, 2, 'How do I add an avatar to my profile?', 'First, find an image that you like, and that is within the rules. Then you will have to find a place to host it, such as our own BitBucket. (Other popular choices are Photobucket, Upload-It! or ImageShack). All that is left to do is copy the URL you were given when uploading it to the avatar field in your profile.\r\n\r\nPlease do not make a post just to test your avatar. If everything is allright you''ll see it in your details page. ', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (18, 3, 'Most common reason for stats not updating', ' * The user is cheating. (a.k.a. "Summary Ban")\r\n * The server is overloaded and unresponsive. Just try to keep the session open until the server responds again. (Flooding the server with consecutive manual updates is not recommended.)\r\n * You are using a faulty client. If you want to use an experimental or CVS version you do it at your own risk.\r\n', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (19, 3, 'Best practices', ' * If a torrent you are currently leeching/seeding is not listed on your profile, just wait or force a manual update.\r\n * Make sure you exit your client properly, so that the tracker receives "event=completed".\r\n * If the tracker is down, do not stop seeding. As long as the tracker is back up before you exit the client the stats should update properly.', 1162676179, 1162406569);
INSERT INTO `faq` VALUES (20, 5, 'How do I use the files I''ve downloaded?', 'Check out this guide http://localhost/TBDEV/formats.php', 1162676179, 1162767711);
INSERT INTO `faq` VALUES (21, 3, 'May I use any bittorrent client?', 'Yes. The tracker now updates the stats correctly for all bittorrent clients. However, we still recommend that you avoid the following clients:\r\n\r\nÃƒÂ¯Ã‚Â¿Ã‚Â½ BitTorrent++,\r\nÃƒÂ¯Ã‚Â¿Ã‚Â½ Nova Torrent,\r\nÃƒÂ¯Ã‚Â¿Ã‚Â½ TorrentStorm.\r\n\r\nThese clients do not report correctly to the tracker when canceling/finishing a torrent session. If you use them then a few MB may not be counted towards the stats near the end, and torrents may still be listed in your profile for some time after you have closed the client.\r\n\r\nAlso, clients in alpha or beta version should be avoided.', 1162676179, 1162768097);
INSERT INTO `faq` VALUES (22, 3, 'Why is a torrent I''m leeching/seeding listed several times in my profile?', 'If for some reason (e.g. pc crash, or frozen client) your client exits improperly and you restart it, it will have a new peer_id, so it will show as a new torrent. The old one will never receive a "event=completed" or "event=stopped" and will be listed until some tracker timeout. Just ignore it, it will eventually go away.', 1162676179, 1162768208);
INSERT INTO `faq` VALUES (23, 8, 'Maybe my address is blacklisted?', 'The site blocks addresses listed in the (former) PeerGuardian database, as well as addresses of banned users. This works at Apache/PHP level, it''s just a script that blocks logins from those addresses. It should not stop you from reaching the site. In particular it does not block lower level protocols, you should be able to ping/traceroute the server even if your address is blacklisted. If you cannot then the reason for the problem lies elsewhere.\r\n\r\nIf somehow your address is indeed blocked in the PG database do not contact us about it, it is not our policy to open ad hoc exceptions. You should clear your IP with the database maintainers instead.', 1163155783, 1162820705);
INSERT INTO `faq` VALUES (24, 8, 'My ISP blocks the site''s address', '(In first place, it''s unlikely your ISP is doing so. DNS name resolution and/or network problems are the usual culprits.)\r\nThere''s nothing we can do. You should contact your ISP (or get a new one). Note that you can still visit the site via a proxy, follow the instructions in the relevant section. In this case it doesn''t matter if the proxy is anonymous or not, or which port it listens to.\r\n\r\nNotice that you will always be listed as an "unconnectable" client because the tracker will be unable to check that you''re capable of accepting incoming connections.', 1163155783, 1162821075);
INSERT INTO `faq` VALUES (25, 8, 'Is there an alternate port (81)?', 'Some of our torrents use ports other than the usual HTTP port 80. This may cause problems for some users, for instance those behind some firewall or proxy configurations. You can easily solve this by editing the .torrent file yourself with any torrent editor, e.g. MakeTorrent, and replacing the announce url torrentbits.org:81 with torrentbits.org:80 or just torrentbits.org.\r\n\r\nEditing the .torrent with Notepad is not recommended. It may look like a text file, but it is in fact a bencoded file. If for some reason you must use a plain text editor, change the announce url to torrentbits.org:80, not torrentbits.org. (If you''re thinking about changing the number before the announce url instead, you know too much to be reading this.)', 1163155783, 1162821157);
INSERT INTO `faq` VALUES (27, 5, 'Downloaded a movie and don''t know what CAM/TS/TC/SCR means?', 'Check out this guide.', 1163165698, 0);
INSERT INTO `faq` VALUES (28, 5, 'Why did an active torrent suddenly disappear?', 'There may be three reasons for this:\r\n(1) The torrent may have been out-of-sync with the site rules.\r\n(2) The uploader may have deleted it because it was a bad release. A replacement will probably be uploaded to take its place.\r\n(3) Torrents are automatically deleted after 28 days.', 1163166050, 0);
INSERT INTO `faq` VALUES (29, 9, 'What if my Question isn''t answered here?', 'Post in the Forums, by all means. You''ll find they are usually a friendly and helpful place, provided you follow a few basic guidelines:\r\n\r\n * Make sure your problem is not really in this FAQ. There''s no point in posting just to be sent back here.\r\n * Before posting read the sticky topics (the ones at the top). Many times new information that still hasn''t been incorporated in the FAQ can be found there.\r\n * Help us in helping you. Do not just say "it doesn''t work!". Provide details so that we don''t have to guess or waste time asking. What client do you use? What''s your OS? What''s your network setup? What''s the exact error message you get, if any? What are the torrents you are having problems with? The more you tell the easiest it will be for us, and the more probable your post will get a reply.\r\n * And needless to say: be polite. Demanding help rarely works, asking for it usually does the trick.', 1163168322, 0);
INSERT INTO `faq` VALUES (30, 9, 'What if I find a rabbit on the tracker?', 'Roadkill! :D', 1163168717, 1162911763);

-- --------------------------------------------------------

-- 
-- Table structure for table `faq_categories`
-- 

CREATE TABLE `faq_categories` (
  `cid` int(3) unsigned NOT NULL auto_increment,
  `fcat_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `faq_categories`
-- 

INSERT INTO `faq_categories` VALUES (1, 'Site Information');
INSERT INTO `faq_categories` VALUES (2, 'User Information');
INSERT INTO `faq_categories` VALUES (3, 'Stats');
INSERT INTO `faq_categories` VALUES (4, 'Uploading');
INSERT INTO `faq_categories` VALUES (5, 'Downloading');
INSERT INTO `faq_categories` VALUES (6, 'How can I improve my download speed?');
INSERT INTO `faq_categories` VALUES (7, 'My ISP uses a transparent proxy. What should I do?');
INSERT INTO `faq_categories` VALUES (8, 'Why can''t I connect? Is the site blocking me?');
INSERT INTO `faq_categories` VALUES (9, 'Miscellaneous');

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) collate utf8_bin NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `files`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `flush_log`
-- 

CREATE TABLE `flush_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `flush_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `forums`
-- 

CREATE TABLE `forums` (
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) collate utf8_bin NOT NULL default '',
  `description` varchar(200) collate utf8_bin default NULL,
  `minclassread` tinyint(3) unsigned NOT NULL default '0',
  `minclasswrite` tinyint(3) unsigned NOT NULL default '0',
  `postcount` int(10) unsigned NOT NULL default '0',
  `topiccount` int(10) unsigned NOT NULL default '0',
  `minclasscreate` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `forums`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `free_download`
-- 

CREATE TABLE `free_download` (
  `free` varchar(4) NOT NULL default 'free',
  `free_for_all` enum('yes','no') NOT NULL default 'no',
  `title` varchar(120) NOT NULL default '',
  `message` text,
  PRIMARY KEY  (`free`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `free_download`
-- 

INSERT INTO `free_download` VALUES ('free', 'no', 'TEST', 'enter message :-P ');

-- --------------------------------------------------------

-- 
-- Table structure for table `freepoll`
-- 

CREATE TABLE `freepoll` (
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `freepoll`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `freeslots`
-- 

CREATE TABLE `freeslots` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrentid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `doubleup` enum('yes','no') default 'no',
  `free` enum('yes','no') default 'no',
  `addedup` date NOT NULL default '0000-00-00',
  `addedfree` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `freeslots`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `friends`
-- 

CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  `confirmed` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `friends`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `funds`
-- 

CREATE TABLE `funds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cash` decimal(8,2) NOT NULL default '0.00',
  `user` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `funds`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gallery_admin`
-- 

CREATE TABLE `gallery_admin` (
  `per_page` smallint(4) NOT NULL default '20',
  `num_rows` tinyint(2) NOT NULL default '20',
  `max_file_size` int(12) NOT NULL default '1048576',
  KEY `per_page` (`per_page`),
  KEY `num_rows` (`num_rows`),
  KEY `max_file_size` (`max_file_size`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `gallery_admin`
-- 

INSERT INTO `gallery_admin` VALUES (20, 5, 402880);

-- --------------------------------------------------------

-- 
-- Table structure for table `gallery_admin_users`
-- 

CREATE TABLE `gallery_admin_users` (
  `user_class` tinyint(3) NOT NULL default '0',
  `gal_per_member` int(4) NOT NULL default '0',
  `number_total` int(4) NOT NULL default '0',
  `number_of_pics` int(4) NOT NULL default '0',
  PRIMARY KEY  (`user_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `gallery_admin_users`
-- 

INSERT INTO `gallery_admin_users` VALUES (0, 0, 0, 0);
INSERT INTO `gallery_admin_users` VALUES (1, 2, 20, 1);
INSERT INTO `gallery_admin_users` VALUES (2, 4, 30, 4);
INSERT INTO `gallery_admin_users` VALUES (3, 6, 40, 6);
INSERT INTO `gallery_admin_users` VALUES (4, 8, 50, 8);
INSERT INTO `gallery_admin_users` VALUES (5, 10, 60, 10);
INSERT INTO `gallery_admin_users` VALUES (6, 12, 100, 10);
INSERT INTO `gallery_admin_users` VALUES (7, 12, 100, 10);

-- --------------------------------------------------------

-- 
-- Table structure for table `happyhour`
-- 

CREATE TABLE `happyhour` (
  `id` int(10) NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `multiplier` float NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`torrentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `happyhour`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `happylog`
-- 

CREATE TABLE `happylog` (
  `id` int(10) NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `torrentid` int(10) NOT NULL default '0',
  `multi` float NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`,`torrentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `happylog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `helpdesk`
-- 

CREATE TABLE `helpdesk` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(60) NOT NULL default '',
  `msg_problem` text,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `solved_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `solved` enum('no','yes','ignored') NOT NULL default 'no',
  `added_by` int(10) NOT NULL default '0',
  `solved_by` int(10) NOT NULL default '0',
  `msg_answer` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `helpdesk`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `image_ratings`
-- 

CREATE TABLE `image_ratings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `image_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `image_ratings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `infolog`
-- 

CREATE TABLE `infolog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `infolog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `invite_codes`
-- 

CREATE TABLE `invite_codes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` varchar(32) NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `invite_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('Pending','Confirmed') NOT NULL default 'Pending',
  PRIMARY KEY  (`id`),
  KEY `sender` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `invite_codes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `iplog`
-- 

CREATE TABLE `iplog` (
  `id` int(100) unsigned NOT NULL auto_increment,
  `ip` varchar(15) character set latin1 collate latin1_bin default NULL,
  `userid` int(10) default NULL,
  `access` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `iplog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `links`
-- 

CREATE TABLE `links` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(3) unsigned NOT NULL default '0',
  `heading` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `ctime` int(11) unsigned NOT NULL default '0',
  `mtime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat_id` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `links_categories`
-- 

CREATE TABLE `links_categories` (
  `cid` int(3) unsigned NOT NULL auto_increment,
  `rcat_name` varchar(100) NOT NULL default '',
  `min_class_read` int(2) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `links_categories`
-- 

INSERT INTO `links_categories` VALUES (1, 'Link to us!', 0);
INSERT INTO `links_categories` VALUES (2, 'Site Links', 0);
INSERT INTO `links_categories` VALUES (3, 'Other Links', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `loginattempts`
-- 

CREATE TABLE `loginattempts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(15) NOT NULL default '',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned` enum('yes','no') NOT NULL default 'no',
  `attempts` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `loginattempts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `messages`
-- 

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `subject` varchar(180) NOT NULL default 'No Subject',
  `msg` mediumtext,
  `unread` enum('yes','no') NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL default '0',
  `location` smallint(6) NOT NULL default '1',
  `saved` enum('no','yes') NOT NULL default 'no',
  `urgent` enum('no','yes') NOT NULL default 'no',
  `draft` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `messages`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `my_gallerys`
-- 

CREATE TABLE `my_gallerys` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `gallery_name` varchar(60) NOT NULL default '',
  `share_gallery` enum('public','private','friends') NOT NULL default 'public',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `my_gallerys`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `sticky` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `news`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `notconnectablepmlog`
-- 

CREATE TABLE `notconnectablepmlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `notconnectablepmlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `offers`
-- 

CREATE TABLE `offers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `name` varchar(225) default NULL,
  `descr` text NOT NULL,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `yeah` int(10) unsigned NOT NULL default '0',
  `against` int(10) unsigned NOT NULL default '0',
  `category` int(11) NOT NULL default '0',
  `comments` int(11) NOT NULL default '0',
  `allowed` enum('allowed','pending','denied') NOT NULL default 'pending',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `offers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `offervotes`
-- 

CREATE TABLE `offervotes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `offerid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `vote` enum('yeah','against') NOT NULL default 'yeah',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `offervotes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `overforums`
-- 

CREATE TABLE `overforums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(200) default NULL,
  `minclassview` tinyint(3) unsigned NOT NULL default '0',
  `forid` tinyint(3) unsigned NOT NULL default '1',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `overforums`
-- 

INSERT INTO `overforums` VALUES (1, 'Announcements', 'All Site Announcements and News', 0, 0, 0);
INSERT INTO `overforums` VALUES (2, 'Off Topic', 'As The Title Says !', 0, 0, 1);
INSERT INTO `overforums` VALUES (3, 'Audio/Video', 'All Audio And Video Posts Here', 0, 0, 2);
INSERT INTO `overforums` VALUES (4, 'Games', 'Online Gamers - Home Pc Gamers - X360 Gamers Here', 0, 0, 3);
INSERT INTO `overforums` VALUES (5, 'Pc', 'All Pc Related Issue''s Here', 0, 0, 4);
INSERT INTO `overforums` VALUES (6, 'Help', 'Site Help , Bug Reports', 0, 0, 5);
INSERT INTO `overforums` VALUES (7, 'Staff Forums', 'All Staff Forums Here', 4, 1, 6);

-- --------------------------------------------------------

-- 
-- Table structure for table `peers`
-- 

CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `peer_id` varchar(20) collate utf8_bin NOT NULL default '',
  `ip` varchar(64) collate utf8_bin NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `started` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `prev_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) collate utf8_bin NOT NULL default '',
  `finishedat` int(10) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  `passkey` varchar(32) collate utf8_bin NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `peers`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `photo_gallery`
-- 

CREATE TABLE `photo_gallery` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(120) NOT NULL default '',
  `location` varchar(240) NOT NULL default '',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `in_gallery` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `photo_gallery`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pmboxes`
-- 

CREATE TABLE `pmboxes` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `boxnumber` tinyint(4) NOT NULL default '2',
  `name` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pmboxes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pollanswers`
-- 

CREATE TABLE `pollanswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `pollanswers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `poller`
-- 

CREATE TABLE `poller` (
  `ID` int(11) NOT NULL auto_increment,
  `pollerTitle` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `poller`
-- 

INSERT INTO `poller` VALUES (1, 'How would you rate this script?');

-- --------------------------------------------------------

-- 
-- Table structure for table `poller_option`
-- 

CREATE TABLE `poller_option` (
  `ID` int(11) NOT NULL auto_increment,
  `pollerID` int(11) default NULL,
  `optionText` varchar(255) default NULL,
  `pollerOrder` int(11) default NULL,
  `defaultChecked` char(1) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `poller_option`
-- 

INSERT INTO `poller_option` VALUES (1, 1, 'Excellent', 0, '0');
INSERT INTO `poller_option` VALUES (2, 1, 'Good', 1, '0');
INSERT INTO `poller_option` VALUES (3, 1, 'Fair', 2, '0');
INSERT INTO `poller_option` VALUES (4, 1, 'Rubbish', 3, '0');
INSERT INTO `poller_option` VALUES (5, 1, 'Dont Care', 4, '0');

-- --------------------------------------------------------

-- 
-- Table structure for table `poller_vote`
-- 

CREATE TABLE `poller_vote` (
  `ID` int(11) NOT NULL auto_increment,
  `optionID` int(11) default NULL,
  `userID` int(11) default NULL,
  `pollerID` int(11) default NULL,
  `ipAddress` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `poller_vote`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `polls`
-- 

CREATE TABLE `polls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` varchar(255) collate utf8_bin NOT NULL default '',
  `option0` varchar(40) collate utf8_bin NOT NULL default '',
  `option1` varchar(40) collate utf8_bin NOT NULL default '',
  `option2` varchar(40) collate utf8_bin NOT NULL default '',
  `option3` varchar(40) collate utf8_bin NOT NULL default '',
  `option4` varchar(40) collate utf8_bin NOT NULL default '',
  `option5` varchar(40) collate utf8_bin NOT NULL default '',
  `option6` varchar(40) collate utf8_bin NOT NULL default '',
  `option7` varchar(40) collate utf8_bin NOT NULL default '',
  `option8` varchar(40) collate utf8_bin NOT NULL default '',
  `option9` varchar(40) collate utf8_bin NOT NULL default '',
  `option10` varchar(40) collate utf8_bin NOT NULL default '',
  `option11` varchar(40) collate utf8_bin NOT NULL default '',
  `option12` varchar(40) collate utf8_bin NOT NULL default '',
  `option13` varchar(40) collate utf8_bin NOT NULL default '',
  `option14` varchar(40) collate utf8_bin NOT NULL default '',
  `option15` varchar(40) collate utf8_bin NOT NULL default '',
  `option16` varchar(40) collate utf8_bin NOT NULL default '',
  `option17` varchar(40) collate utf8_bin NOT NULL default '',
  `option18` varchar(40) collate utf8_bin NOT NULL default '',
  `option19` varchar(40) collate utf8_bin NOT NULL default '',
  `sort` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `location` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `location` (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `polls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `postpollanswers`
-- 

CREATE TABLE `postpollanswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `postpollanswers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `postpolls`
-- 

CREATE TABLE `postpolls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` text NOT NULL,
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
  `sort` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `postpolls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topicid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `body` longtext collate utf8_bin,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `post_history` mediumtext collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `posts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ratings`
-- 

CREATE TABLE `ratings` (
  `torrent` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `rating` tinyint(3) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `topic` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`torrent`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `ratings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `readposts`
-- 

CREATE TABLE `readposts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  `lastpostread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `readposts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `reported_by` int(10) unsigned NOT NULL default '0',
  `reporting_what` int(10) unsigned NOT NULL default '0',
  `reporting_type` enum('User','Comment','Request_Comment','Offer_Comment','Request','Offer','Torrent','Hit_And_Run','Post') NOT NULL default 'Torrent',
  `reason` text NOT NULL,
  `who_delt_with_it` int(10) unsigned NOT NULL default '0',
  `delt_with` tinyint(1) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `how_delt_with` text NOT NULL,
  `2nd_value` int(10) unsigned NOT NULL default '0',
  `when_delt_with` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `reports`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `requests`
-- 

CREATE TABLE `requests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `request` varchar(225) default NULL,
  `descr` text NOT NULL,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL default '0',
  `cat` int(10) unsigned NOT NULL default '0',
  `filledby` int(10) unsigned NOT NULL default '0',
  `filledurl` varchar(70) default NULL,
  `filled` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `id_added` (`id`,`added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `requests`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `rules`
-- 

CREATE TABLE `rules` (
  `id` int(11) NOT NULL auto_increment,
  `cid` int(3) unsigned NOT NULL default '0',
  `heading` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `ctime` int(11) unsigned NOT NULL default '0',
  `mtime` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat_id` (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `rules`
-- 

INSERT INTO `rules` VALUES (1, 1, ':: ::General rules - Breaking these rules can and will get you banned !', 'Access to the newest torrents is conditional on a good ratio! (See the FAQ  for details.)\r\nLow ratios may result in severe consequences, including banning in extreme cases.\r\n\r\nGeneral Guidelines - Please follow these guidelines or else you might end up with a warning!\r\n\r\nNo duplicate accounts from the same IP.   Members with more than one account for whatever reason without approval of Sysops or staff leaders will get banned  please do not make multiple  accounts!.\r\nNo aggressive behavior or flaming in the forums.\r\nNo trashing of other peoples topics (e.g. SPAM)\r\nNo language other than English in the forums.\r\n\r\nThis site has very strict rules in regards to racial slurs and racist remarks', 1214338879, 1214079783);
INSERT INTO `rules` VALUES (2, 2, ':: ::Forum Rules', ' # Please, feel free to answer any questions but leave the moderating to the moderators.\r\n     # Don''t use all capital letters, excessive !!! (exclamation marks) or ??? (question marks)... it seems like you''re shouting.\r\n     # No posting of users stats without their consent is allowed in the forums or torrent comments regardless of ratio or class.  \r\n     # No trashing of other peoples topics.\r\n     # No systematic foul language (and none at all on titles).\r\n     # No double posting. If you wish to post again, and yours is the last post in the thread please use the EDIT function, instead of posting a double.    \r\n     # No bumping... (All bumped threads will be Locked.)  \r\n     # No direct links to internet sites in the forums.      \r\n     # No images larger than 400x400, and preferably web-optimised. Use the [imgw] tag for larger images.\r\n     # No advertising, mechandising or promotions of any sort are allowed on the site.    \r\n     # Do not tell people to read the Rules, the FAQ, or comment on their ratios and torrents.    \r\n     # No consistent off-topic posts allowed in the forums. (i.e. SPAM or hijacking)  \r\n     # The Trading/Requesting of invites to other sites is forbidden in the forums.  \r\n     # Do not post links to other torrent sites or torrents on those sites.    \r\n     # Users are not allowed, under any circumstance to create their own polls in the forum.    \r\n     # No self-congratulatory topics are allowed in the forums.    \r\n     # Do not quote excessively. One quote of a quote box is sufficient.    \r\n     # Please ensure all questions are posted in the correct section!     (Game questions in the Games section, Apps questions in the Apps section, etc.)    \r\n     # Please, feel free to answer any questions.. However remain respectful to the people you help ....nobodys better than anyone else.    \r\n     # Last, please read the FAQ before asking any question', 1214339023, 0);
INSERT INTO `rules` VALUES (3, 4, ':: ::Uploaders Rules', 'All uploaders are subject to follow the below rules in order to be a part of the  uploader team. We realize that it''s quite a list, and for new uploaders, it might seem a bit overwhelming, but as you spend time here, they''ll become second hat.\r\n\r\nTo apply to become a site uploader use the uploaders application form, contact staff to get the link.\r\n\r\nTorrents that do not follow the rules below will be deleted.  If you have any questions about the below rules, please feel free to PM them and I will clarify as best I can.\r\n\r\nWelcome to the team and happy uploading!\r\n\r\n# All Uploaders must upload a minimum of 3 unique torrents each week to retain their Uploader status.  Failure to comply will result in a demotion, and a minimum of a 2 week blackout period where they will not be able to return to the Uploader team.  If, after the 2 weeks, the Uploader can prove they will be active, they will be reinstated.  A second instance of inactivity will be cause for permanent removal from the Uploader team.  Extenuating circumstances will be considered if it is the cause of inactivity.  If you are going to be away, please let a staff member know so that your account is not affected.\r\n# All torrents must be rarred, no matter what the size or type.  The ONLY exception to this is MP3s.  Guidelines for rarring your own releases are as follows:\r\n', 1214339203, 0);
INSERT INTO `rules` VALUES (4, 5, ':: ::Free leech rules', '      From time to time we will have freeleech for 48hours. This means that when you download from site it will not count against your download ratio.\r\n\r\n      Whatever you seed back will add to your upload ratio.\r\n\r\nThis is a good opportunity for members with ratio''s below 1.0 to bring them back into line\r\n\r\nAnyone who hit and runs on a freeleech torrent will receive a mandatory 2 week warning. You must seed all torrents downloaded to  100% or for a minimum of 48 hours this is for free leech torrents only.\r\n\r\n', 1214339464, 0);
INSERT INTO `rules` VALUES (5, 6, ':: ::Downloading rules', 'No comments on torrents you are not about to download\r\nOnce download is complete, remember to seed for as long as possible or for a minimum of 36 hours or a ratio of 1:1\r\nLow ratios will be given the three strike warning from staff and can lead to a total ban', 1214339531, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `rules_categories`
-- 

CREATE TABLE `rules_categories` (
  `cid` int(3) unsigned NOT NULL auto_increment,
  `rcat_name` varchar(100) NOT NULL default '',
  `min_class_read` int(2) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `rules_categories`
-- 

INSERT INTO `rules_categories` VALUES (1, ':: ::General Site Rules', 0);
INSERT INTO `rules_categories` VALUES (2, ':: ::Forum Rules', 0);
INSERT INTO `rules_categories` VALUES (5, ':: ::Free leech rules', 0);
INSERT INTO `rules_categories` VALUES (3, ':: ::Uploaders Rules', 0);
INSERT INTO `rules_categories` VALUES (6, ':: ::Downloading rules', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `shit_list`
-- 

CREATE TABLE `shit_list` (
  `userid` int(10) unsigned NOT NULL default '0',
  `suspect` int(10) unsigned NOT NULL default '0',
  `shittyness` int(2) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `shit_list`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `shoutbox`
-- 

CREATE TABLE `shoutbox` (
  `id` bigint(10) NOT NULL auto_increment,
  `userid` bigint(6) NOT NULL default '0',
  `username` varchar(25) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `shoutbox`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sitelog`
-- 

CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `txt` text collate utf8_bin,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `sitelog`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `siteonline`
-- 

CREATE TABLE `siteonline` (
  `onoff` int(1) NOT NULL default '1',
  `reason` varchar(255) NOT NULL default '',
  `class` int(2) NOT NULL default '7',
  `class_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`onoff`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `siteonline`
-- 

INSERT INTO `siteonline` VALUES (1, 'Server Offline For Updates - Back Soon !', 0, 'just for User');

-- --------------------------------------------------------

-- 
-- Table structure for table `snatched`
-- 

CREATE TABLE `snatched` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `connectable` enum('yes','no') NOT NULL default 'no',
  `agent` varchar(60) NOT NULL default '',
  `peer_id` varchar(20) NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `upspeed` bigint(20) NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `downspeed` bigint(20) NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `seedtime` int(10) unsigned NOT NULL default '0',
  `leechtime` int(10) unsigned NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `complete_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `timesann` bigint(10) unsigned NOT NULL default '0',
  `ctimesann` bigint(10) unsigned NOT NULL default '0',
  `hit_run` int(2) default '0',
  `prewarn` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished` enum('yes','no') NOT NULL default 'no',
  `sl_warned` enum('yes','no') NOT NULL default 'no',
  `torrent_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tr_usr` (`torrentid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `snatched`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `staffmessages`
-- 

CREATE TABLE `staffmessages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `msg` text,
  `subject` varchar(100) NOT NULL default '',
  `answeredby` int(10) unsigned NOT NULL default '0',
  `answered` tinyint(1) NOT NULL default '0',
  `answer` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `staffmessages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `staffpanel`
-- 

CREATE TABLE `staffpanel` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_name` varchar(30) NOT NULL default '',
  `file_name` varchar(30) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `av_class` tinyint(3) unsigned NOT NULL default '0',
  `added_by` int(10) unsigned NOT NULL default '0',
  `added` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `file_name` (`file_name`),
  KEY `av_class` (`av_class`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `staffpanel`
-- 

INSERT INTO `staffpanel` VALUES (1, 'Ratio Edit', 'ratioedit', 'Adjust Members Ratio Here', 6, 1, 1217836568);
INSERT INTO `staffpanel` VALUES (2, 'Optimise Database', 'mysql_overview', 'View And Optimise The Database From The Tracker', 7, 1, 1217836655);
INSERT INTO `staffpanel` VALUES (3, 'Uploader Applications', 'uploadapps', 'View All New Uploader Applications', 6, 1, 1217836885);
INSERT INTO `staffpanel` VALUES (4, 'Manage Chmod', 'chmod', 'Manage Ftp Directory Chmod Permissions', 7, 1, 1217836942);
INSERT INTO `staffpanel` VALUES (5, 'Manual Clean up', 'docleanup', 'Run Site Clean Up ', 6, 1, 1217837040);
INSERT INTO `staffpanel` VALUES (6, 'Mass Bonus', 'massbonus', 'Award All Members 500 Karma Points', 6, 1, 1217837358);
INSERT INTO `staffpanel` VALUES (7, 'Cache Stylesheets', 'cachestylesheets', 'Cache The StyleSheets', 6, 1, 1217837552);
INSERT INTO `staffpanel` VALUES (8, 'Ip Bans', 'bans', 'Ip Ban Bad Users', 6, 1, 1217838226);
INSERT INTO `staffpanel` VALUES (9, 'Donations', 'donations', 'View All Site Donators And Donations Here', 6, 1, 1217838383);
INSERT INTO `staffpanel` VALUES (10, 'Site Check', 'sitecheck', 'Coders Site Checker', 7, 1, 1217838638);
INSERT INTO `staffpanel` VALUES (11, 'Hack Log', 'hacklog', 'Monitor Possible Xss Hack Attempts', 6, 1, 1217838737);
INSERT INTO `staffpanel` VALUES (12, 'Client Viewer', 'detectclients', 'View Clients Peer Id For Banning Clients', 6, 1, 1217838860);
INSERT INTO `staffpanel` VALUES (13, 'Db Admin', 'msgspy', 'Delete And Manage Spam', 6, 1, 1217839090);
INSERT INTO `staffpanel` VALUES (15, 'Slots Manager', 'manage-slots', 'Manage Members Freeslots', 6, 1, 1217839273);
INSERT INTO `staffpanel` VALUES (16, 'Delete User', 'delacctadmin', 'Delete Members Accounts', 5, 1, 1217839394);
INSERT INTO `staffpanel` VALUES (17, 'Check Torrent Comments', 'torrentcomments', 'Check Comments For Karma Whores', 5, 1, 1217839483);
INSERT INTO `staffpanel` VALUES (18, 'Mass Pm', 'masspm', 'Mass Pm All Members', 5, 1, 1217840638);
INSERT INTO `staffpanel` VALUES (19, 'View Reports', 'reports', 'View All Site Reports', 5, 1, 1217840706);
INSERT INTO `staffpanel` VALUES (20, 'Flush Log', 'flush_log', 'View All Members Flushes', 5, 1, 1217841092);
INSERT INTO `staffpanel` VALUES (21, 'Announcements', 'usersearch', 'Create New Announcement', 5, 1, 1217843007);
INSERT INTO `staffpanel` VALUES (22, 'Banned Clients', 'client_clearban', 'View Banned And Ban Clients', 5, 1, 1217843161);
INSERT INTO `staffpanel` VALUES (23, 'Invite Manager', 'inviteadd', 'Manage Members Invites', 5, 1, 1217843289);
INSERT INTO `staffpanel` VALUES (24, 'Reset ShoutBox', 'resetshoutbox', 'Clear Old Shout Box History', 5, 1, 1217853919);
INSERT INTO `staffpanel` VALUES (25, 'Edit Faq', 'faqadmin', 'Edit Site Faq Categories', 5, 1, 1217854009);
INSERT INTO `staffpanel` VALUES (27, 'Bonus Manager', 'bonusmanage', 'Manage Site Bonus Options', 5, 1, 1217854158);
INSERT INTO `staffpanel` VALUES (28, 'Add User', 'adduser', 'Manually Create A New Account', 5, 1, 1217854252);
INSERT INTO `staffpanel` VALUES (29, 'Advanced Account Manager', 'acpmanage', 'Manage Bans - Disabled - Pending Users', 5, 1, 1217854481);
INSERT INTO `staffpanel` VALUES (30, 'Reset Banned', 'maxlogin', 'Reset Banned Failed Login Attempts', 5, 1, 1217854628);
INSERT INTO `staffpanel` VALUES (31, 'Edit Rules', 'rules_admin', 'Edit Site Rules', 5, 1, 1217856210);
INSERT INTO `staffpanel` VALUES (33, 'Edit Links', 'links_admin', 'Edit Site Links', 5, 1, 1217856548);
INSERT INTO `staffpanel` VALUES (34, 'Warned User''s', 'warned', 'Manage Warned Users', 4, 1, 1217861230);
INSERT INTO `staffpanel` VALUES (35, 'Rip Nfo', 'nforipper', 'Rip Ascii From Nfo''s', 4, 1, 1217861313);
INSERT INTO `staffpanel` VALUES (36, 'Invited Users', 'invitedby', 'Show All Invited Users', 4, 1, 1217861373);
INSERT INTO `staffpanel` VALUES (37, 'Uploader Activity', 'stats', 'View Uploader Activity And Categorie Activity', 4, 1, 1217861859);
INSERT INTO `staffpanel` VALUES (38, 'Category Manager', 'categorie', 'Manage Site Categories', 4, 1, 1217861924);
INSERT INTO `staffpanel` VALUES (39, 'Name Changer', 'namechanger', 'Change Members Nicks', 4, 1, 1217862003);
INSERT INTO `staffpanel` VALUES (40, 'Site Log', 'log', 'View All Site log Entrys', 4, 1, 1217862053);
INSERT INTO `staffpanel` VALUES (41, 'User List', 'users', 'Full Site User List', 4, 1, 1217862162);
INSERT INTO `staffpanel` VALUES (42, 'Forum Manager', 'forummanage', 'Manage And Edit Forums', 7, 1, 1217862210);
INSERT INTO `staffpanel` VALUES (43, 'Inactive Users', 'inactive', 'Show All Inactive Members - Notify By Email', 4, 1, 1217862406);
INSERT INTO `staffpanel` VALUES (44, 'Reset Password', 'reset', 'Reset Forgotten Passwords', 4, 1, 1217862448);
INSERT INTO `staffpanel` VALUES (45, 'Snatched Torrents', 'snatched_torrents', 'View All Site Snatches', 4, 1, 1217862509);
INSERT INTO `staffpanel` VALUES (46, 'Duplicate Ip''s', 'ipcheck', 'Check Site For Duplicate Ip''s', 4, 1, 1217862581);
INSERT INTO `staffpanel` VALUES (47, 'Not Connectable', 'findnotconnectable', 'View All Non-Connectable Members', 4, 1, 1217862663);
INSERT INTO `staffpanel` VALUES (48, 'Site Peers', 'viewpeers', 'View All Site Peers', 4, 1, 1217862722);
INSERT INTO `staffpanel` VALUES (49, 'Free Leech', 'freeleech', 'Free Leech For All', 5, 1, 1217886796);
INSERT INTO `staffpanel` VALUES (50, 'Advanced User Search', 'usersearch1', 'Carry Out Advanced User Searches', 4, 1, 1218321784);
INSERT INTO `staffpanel` VALUES (51, 'HtAccessor', 'htaccesser', 'Make .Htaccess files', 7, 1, 1218993346);
INSERT INTO `staffpanel` VALUES (53, 'Check Invites', 'invitesinplay', 'Keep Tabs on invite''s ', 6, 1, 1219524702);
INSERT INTO `staffpanel` VALUES (55, ' Db Manager', 'database', 'Back Up Db', 7, 1, 1219602892);
INSERT INTO `staffpanel` VALUES (56, 'Cache Countries', 'cachecountries', 'Cache Countries When Adding New Entrys', 6, 1, 1219664241);
INSERT INTO `staffpanel` VALUES (57, 'Cache Categories', 'cachecategories', 'Cache Catigories When Adding New Entry', 6, 1, 1219664338);
INSERT INTO `staffpanel` VALUES (58, 'View Shout History', 'shistory', 'Shout History Check', 6, 1, 1220057674);
INSERT INTO `staffpanel` VALUES (59, 'Php File Edit Log', 'editlog', 'Coders Php File Edit Log', 7, 1, 1220104851);
INSERT INTO `staffpanel` VALUES (60, 'Sql Query Script', 'sqlcmdex', 'Execute Raw Sql Commands From Tracker', 7, 1, 1220181900);
INSERT INTO `staffpanel` VALUES (61, 'Users Possibilities', 'userspos', 'View User Settings', 4, 1, 1220640589);
INSERT INTO `staffpanel` VALUES (62, 'PhpInfo', 'system_view', 'PhpInfo - Check Filepaths And Configs', 7, 1, 1220781999);
INSERT INTO `staffpanel` VALUES (63, 'Byte Calculator', 'calculator', 'Convert gigbytes to bytes ect for ratio adjustments', 4, 1, 1221381023);
INSERT INTO `staffpanel` VALUES (65, 'Reveal Ip Location', 'iptocountry', 'Show Geo Location On Any Ip', 5, 1, 1221406559);
INSERT INTO `staffpanel` VALUES (66, 'Ban Spoof Emails', 'bannedemails', 'Ban Fake Email Address From Being Used On Sign Up', 5, 1, 1221937606);
INSERT INTO `staffpanel` VALUES (67, 'Mass Freeleech', 'massfree', 'Not All FreeDownload - Sets Individual Torrent''s To  Countstats On Or Off', 4, 1, 1222420548);
INSERT INTO `staffpanel` VALUES (68, 'Proxy Detect', 'proxy', 'Possible Proxy Users May Be Listed Here', 7, 1, 1222431165);
INSERT INTO `staffpanel` VALUES (69, 'Site Offline Control', 'siteonoff', 'Turn Site Offline - Staff access Only', 7, 1, 1223213931);
INSERT INTO `staffpanel` VALUES (70, 'Staff Actions Log', 'sysoplog', 'Staff Functions Log', 6, 1, 1224993991);
INSERT INTO `staffpanel` VALUES (71, 'Passkey Checker', 'selpasskey', 'View Users With More Than One Passkey', 5, 1, 1228089465);
INSERT INTO `staffpanel` VALUES (72, 'Manage Onsite Staff', 'maxcoder', 'Add new staff members here - Warning Be Careful When Using This !', 7, 1, 1231104615);
INSERT INTO `staffpanel` VALUES (73, 'Ratio Cheaters', 'cheaters', 'Check Abnormal Upload Speeds', 5, 1, 1231966160);
INSERT INTO `staffpanel` VALUES (74, 'Create A Countdown Event', 'countdown', 'Countdown Timer Control', 6, 1, 1231966233);

-- --------------------------------------------------------

-- 
-- Table structure for table `stylesheets`
-- 

CREATE TABLE `stylesheets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `stylesheets`
-- 

INSERT INTO `stylesheets` VALUES (1, 'default', '(default)');
INSERT INTO `stylesheets` VALUES (2, 'large', 'Large text');

-- --------------------------------------------------------

-- 
-- Table structure for table `subscriptions`
-- 

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `subscriptions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `thanks`
-- 

CREATE TABLE `thanks` (
  `tid` bigint(10) NOT NULL auto_increment,
  `uid` bigint(10) NOT NULL default '0',
  `torid` bigint(10) NOT NULL default '0',
  `thank_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `thanks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `topics`
-- 

CREATE TABLE `topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(40) collate utf8_bin default NULL,
  `locked` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `forumid` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `views` int(10) unsigned NOT NULL default '0',
  `pollid` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `subject` (`subject`),
  KEY `lastpost` (`lastpost`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `topics`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `torrents`
-- 

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `filename` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `save_as` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `search_text` text character set utf8 collate utf8_bin NOT NULL,
  `descr` text character set utf8 collate utf8_bin NOT NULL,
  `ori_descr` text character set utf8 collate utf8_bin NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') character set utf8 collate utf8_bin NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'yes',
  `banned` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text character set utf8 collate utf8_bin NOT NULL,
  `points` int(10) NOT NULL default '0',
  `thanks` int(10) NOT NULL default '0',
  `anonymous` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `countstats` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'yes',
  `multiplicator` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `scene` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `request` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  `poster` varchar(255) character set utf8 collate utf8_bin NOT NULL default 'poster.jpg',
  `url` varchar(150) character set utf8 collate utf8_bin default NULL,
  `nuked` enum('yes','no','unnuked') character set utf8 collate utf8_bin NOT NULL default 'no',
  `nukereason` varchar(100) character set utf8 collate utf8_bin NOT NULL default '',
  `tube` varchar(80) character set utf8 collate utf8_bin NOT NULL default '',
  `newgenre` varchar(100) character set utf8 collate utf8_bin NOT NULL default '',
  `afterpre` text character set utf8 collate utf8_bin,
  `uclass` int(10) unsigned NOT NULL,
  `checked_by` varchar(40) character set utf8 collate utf8_bin NOT NULL default '',
  `vip` enum('yes','no') character set utf8 collate utf8_bin default 'no',
  `recommended` enum('yes','no') character set utf8 collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `torrents`
-- 
-- --------------------------------------------------------

-- 
-- Table structure for table `uploadapp`
-- 

CREATE TABLE `uploadapp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `applied` datetime NOT NULL default '0000-00-00 00:00:00',
  `speed` varchar(20) NOT NULL default '',
  `offer` longtext NOT NULL,
  `reason` longtext NOT NULL,
  `sites` enum('yes','no') NOT NULL default 'no',
  `sitenames` varchar(150) NOT NULL default '',
  `scene` enum('yes','no') NOT NULL default 'no',
  `creating` enum('yes','no') NOT NULL default 'no',
  `seeding` enum('yes','no') NOT NULL default 'no',
  `connectable` enum('yes','no','pending') NOT NULL default 'pending',
  `status` enum('accepted','rejected','pending') NOT NULL default 'pending',
  `moderator` varchar(40) NOT NULL default '',
  `comment` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `users` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `uploadapp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `usercomments`
-- 

CREATE TABLE `usercomments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text character set latin1 collate latin1_general_ci NOT NULL,
  `ori_text` text character set latin1 collate latin1_general_ci NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `usercomments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `userhits`
-- 

CREATE TABLE `userhits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `hitid` int(10) unsigned NOT NULL default '0',
  `number` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `added` (`added`),
  KEY `hitid` (`hitid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `userhits`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) collate utf8_bin NOT NULL default '',
  `old_password` varchar(40) collate utf8_bin NOT NULL default '',
  `passhash` varchar(32) collate utf8_bin NOT NULL default '',
  `secret` varchar(20) collate utf8_bin NOT NULL default '',
  `email` varchar(80) collate utf8_bin NOT NULL default '',
  `status` enum('pending','confirmed') collate utf8_bin NOT NULL default 'pending',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `pm_max` tinyint(3) unsigned NOT NULL default '20',
  `pm_count` tinyint(3) unsigned NOT NULL default '0',
  `post_max` tinyint(3) unsigned NOT NULL default '20',
  `post_count` tinyint(3) unsigned NOT NULL default '0',
  `comment_max` tinyint(3) unsigned NOT NULL default '20',
  `comment_count` tinyint(3) unsigned NOT NULL default '0',
  `curr_ann_last_check` datetime NOT NULL default '0000-00-00 00:00:00',
  `curr_ann_id` int(10) unsigned NOT NULL default '0',
  `editsecret` varchar(20) collate utf8_bin NOT NULL default '',
  `privacy` enum('strong','normal','low') collate utf8_bin NOT NULL default 'normal',
  `stylesheet` int(10) default '1',
  `info` text collate utf8_bin,
  `acceptpms` enum('yes','friends','no') collate utf8_bin NOT NULL default 'yes',
  `ip` varchar(15) collate utf8_bin NOT NULL default '',
  `class` tinyint(3) unsigned NOT NULL default '0',
  `override_class` tinyint(3) unsigned NOT NULL default '255',
  `avatar` varchar(100) collate utf8_bin NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '2147483648',
  `downloaded` bigint(20) unsigned NOT NULL default '1024',
  `title` varchar(30) collate utf8_bin NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(1000) collate utf8_bin NOT NULL,
  `modcomment` text collate utf8_bin NOT NULL,
  `enabled` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `avatars` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `donor` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `warned` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `warneduntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `savepms` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `passkey` varchar(32) collate utf8_bin NOT NULL default '',
  `seedbonus` decimal(10,1) NOT NULL default '200.0',
  `bonuscomment` text collate utf8_bin,
  `vip_added` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `vip_until` datetime NOT NULL default '0000-00-00 00:00:00',
  `smile_until` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_post` datetime NOT NULL default '0000-00-00 00:00:00',
  `forum_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `forumpost` enum('yes','no') character set latin1 collate latin1_general_ci NOT NULL default 'yes',
  `donated` decimal(8,2) NOT NULL default '0.00',
  `donoruntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `total_donated` decimal(8,2) NOT NULL default '0.00',
  `chatpost` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `show_shout` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `signatures` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `signature` varchar(225) collate utf8_bin NOT NULL default '',
  `uploadpos` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `downloadpos` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `support` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `supportfor` text collate utf8_bin NOT NULL,
  `subscription_pm` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `parked` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `anonymous` enum('yes','no') collate utf8_bin default NULL,
  `tenpercent` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `freeslots` int(10) NOT NULL default '5',
  `gotgift` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `last_browse` int(11) NOT NULL default '0',
  `leechwarn` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `leechwarnuntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastwarned` datetime NOT NULL default '0000-00-00 00:00:00',
  `timeswarned` int(10) NOT NULL default '0',
  `warnedby` varchar(40) collate utf8_bin NOT NULL default '',
  `showfriends` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `imagecats` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `port` smallint(5) unsigned NOT NULL default '0',
  `agent` varchar(60) collate utf8_bin NOT NULL,
  `hit_run_total` int(9) default '0',
  `casagree` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `bjwins` int(10) NOT NULL default '0',
  `bjlosses` int(10) NOT NULL default '0',
  `ttablehl` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `split` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `anonymoustopten` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `lastchange` datetime NOT NULL default '0000-00-00 00:00:00',
  `gender` enum('Male','Female','N/A') collate utf8_bin NOT NULL default 'N/A',
  `tohp` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `shoutboxbg` enum('1','2','3') collate utf8_bin NOT NULL default '1',
  `comments` int(10) unsigned NOT NULL default '0',
  `casinoban` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `blackjackban` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `webseeder` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `disableuntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `addbookmark` enum('yes','no','ratio') collate utf8_bin NOT NULL default 'no',
  `bookmcomment` text collate utf8_bin NOT NULL,
  `view_uclass` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `forumview` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `hash1` varchar(96) collate utf8_bin NOT NULL default '',
  `invitedby` int(10) NOT NULL default '0',
  `invitees` varchar(100) collate utf8_bin NOT NULL default '',
  `invites` int(10) NOT NULL default '1',
  `invitedate` datetime NOT NULL default '0000-00-00 00:00:00',
  `invite_on` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `update_new` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `whywarned` text collate utf8_bin NOT NULL,
  `warns` bigint(3) unsigned NOT NULL default '0',
  `dlremoveuntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `immun` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `passhint` int(10) unsigned NOT NULL,
  `hintanswer` varchar(40) collate utf8_bin NOT NULL default '',
  `download` int(10) unsigned NOT NULL default '0',
  `upload` int(10) unsigned NOT NULL default '0',
  `timezone` smallint(3) NOT NULL default '0',
  `dst` tinyint(2) NOT NULL default '0',
  `hidecur` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `tlimitall` int(10) NOT NULL default '0',
  `tlimitseeds` int(10) NOT NULL default '0',
  `tlimitleeches` int(10) NOT NULL default '0',
  `mood` int(10) NOT NULL default '1',
  `birthday` date default '0000-00-00',
  `rohp` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `hits` int(10) NOT NULL default '0',
  `pms_per_page` tinyint(3) unsigned default '50',
  `show_pm_avatar` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `bohp` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `oldpasskey` varchar(32) collate utf8_bin NOT NULL default '',
  `highspeed` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `passkey` (`passkey`),
  KEY `username_2` (`oldpasskey`,`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Dumping data for table `users`
-- 