-- phpMyAdmin SQL Dump
-- version 2.11.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 06, 2008 at 05:56 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
--
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `announcement_process`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachmentdownloads`
--

CREATE TABLE `attachmentdownloads` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL default '',
  `fileid` int(10) NOT NULL default '0',
  `username` varchar(50) NOT NULL default '',
  `userid` int(10) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `downloads` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

INSERT INTO `avps` (`arg`, `value_s`, `value_i`, `value_u`, `value_d`) VALUES
('lastcleantime', '', 0, 0, '0000-00-00 00:00:00'),
('last24', '0', 0, 0, '0000-00-00 00:00:00'),
('extscrape', '0', 0, 0, '0000-00-00 00:00:00'),
('bestfilmofweek', '0', 0, 0, '0000-00-00 00:00:00'),
('inactivemail', '0', 0, 0, '0000-00-00 00:00:00'),
('today', '0', 0, 0, '0000-00-00 00:00:00');

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

INSERT INTO `bannedemails` (`id`, `added`, `addedby`, `comment`, `email`) VALUES
(159, '2007-06-19 23:37:08', 1, 'Fake provider', '*@emailias.com'),
(158, '2007-06-19 23:37:08', 1, 'Fake provider', '*@e4ward.com'),
(157, '2007-06-19 23:37:08', 1, 'Fake provider', '*@dumpmail.de'),
(156, '2007-06-19 23:37:08', 1, 'Fake provider', '*@dontreg.com'),
(155, '2007-06-19 23:37:08', 1, 'Fake provider', '*@disposeamail.com'),
(154, '2007-06-19 23:37:08', 1, 'Fake provider', '*@antispam24.de'),
(153, '2007-06-19 23:37:08', 1, 'Fake provider', '*@trash-mail.de'),
(152, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spambog.de'),
(151, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spambog.com'),
(150, '2007-06-19 23:37:08', 1, 'Fake provider', '*@discardmail.com'),
(149, '2007-06-19 23:37:08', 1, 'Fake provider', '*@discardmail.de'),
(148, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailinator.com'),
(147, '2007-06-19 23:37:08', 1, 'Fake provider', '*@wuzup.net'),
(146, '2007-06-19 23:37:08', 1, 'Fake provider', '*@junkmail.com'),
(145, '2007-06-19 23:37:08', 1, 'Fake provider', '*@clarkgriswald.net'),
(144, '2007-06-19 23:37:08', 1, 'Fake provider', '*@2prong.com'),
(143, '2007-06-19 23:37:08', 1, 'Fake provider', '*@jrwilcox.com'),
(142, '2007-06-19 23:37:08', 1, 'Fake provider', '*@10minutemail.com'),
(141, '2007-06-19 23:37:08', 1, 'Fake provider', '*@pookmail.com'),
(140, '2007-06-19 23:37:08', 1, 'Fake provider', '*@golfilla.info'),
(139, '2007-06-19 23:37:08', 1, 'Fake provider', '*@afrobacon.com'),
(138, '2007-06-19 23:37:08', 1, 'Fake provider', '*@senseless-entertainment.com'),
(137, '2007-06-19 23:37:08', 1, 'Fake provider', '*@put2.net'),
(136, '2007-06-19 23:37:08', 1, 'Fake provider', '*@temporaryinbox.com'),
(135, '2007-06-19 23:37:08', 1, 'Fake provider', '*@slaskpost.se'),
(161, '2007-06-19 23:37:08', 1, 'Fake provider', '*@haltospam.com'),
(162, '2007-06-19 23:37:08', 1, 'Fake provider', '*@h8s.org'),
(163, '2007-06-19 23:37:08', 1, 'Fake provider', '*@ipoo.org'),
(164, '2007-06-19 23:37:08', 1, 'Fake provider', '*@oopi.org'),
(165, '2007-06-19 23:37:08', 1, 'Fake provider', '*@poofy.org'),
(166, '2007-06-19 23:37:08', 1, 'Fake provider', '*@jetable.org'),
(167, '2007-06-19 23:37:08', 1, 'Fake provider', '*@kasmail.com'),
(168, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mail-filter.com'),
(169, '2007-06-19 23:37:08', 1, 'Fake provider', '*@maileater.com'),
(170, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailexpire.com'),
(171, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailnull.com'),
(172, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mailshell.com'),
(173, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mymailoasis.com'),
(174, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mytrashmail.com'),
(175, '2007-06-19 23:37:08', 1, 'Fake provider', '*@mytrashmail.net'),
(176, '2007-06-19 23:37:08', 1, 'Fake provider', '*@shortmail.net'),
(177, '2007-06-19 23:37:08', 1, 'Fake provider', '*@sneakemail.com'),
(178, '2007-06-19 23:37:08', 1, 'Fake provider', '*@sofort-mail.de'),
(179, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamcon.org'),
(180, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamday.com'),
(181, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamex.com'),
(182, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamgourmet.com'),
(183, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spamhole.com'),
(184, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spammotel.com'),
(185, '2007-06-19 23:37:08', 1, 'Fake provider', '*@tempemail.net'),
(186, '2007-06-19 23:37:08', 1, 'Fake provider', '*@tempinbox.com'),
(187, '2007-06-19 23:37:08', 1, 'Fake provider', '*@throwaway.de'),
(188, '2007-06-19 23:37:08', 1, 'Fake provider', '*@woodyland.org'),
(189, '2007-06-19 23:37:08', 1, 'Fake provider', '*@iximail.com'),
(190, '2007-06-19 23:37:08', 1, 'Fake provider', '*@iheartspam.org'),
(191, '2007-06-19 23:37:08', 1, 'Fake provider', '*@spaml.com'),
(192, '2007-06-19 23:37:08', 1, 'Fake provider', '*@noclickemail.com'),
(193, '2007-06-19 23:37:08', 1, 'Fake provider', '*@0clickemail.com'),
(194, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmai.com'),
(195, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hitmail.com'),
(196, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hoitmail.com'),
(197, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@homtail.com'),
(198, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmail.se'),
(199, '2007-06-19 23:37:08', 1, 'Misspelled hotmail?', '*@hotmal.com'),
(200, '2007-06-19 23:37:08', 1, 'Misspelled provider?', '*@bredbnd.net'),
(201, '2008-09-20 20:02:14', 1, 'Spoof Hotmail', '@hiotmail.com'),
(202, '2008-12-06 15:43:07', 1, 'fake', '*@free1houremail.com');

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

INSERT INTO `bans` (`id`, `added`, `addedby`, `comment`, `first`, `last`) VALUES
(3, '2008-08-16 08:38:37', 3, 'test', 1684300900, 1684300900),
(4, '2008-08-30 16:38:37', 1, 'test', 1684300902, 1684300903);

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

INSERT INTO `bonus` (`id`, `enabled`, `bonusname`, `points`, `description`, `art`, `menge`, `pointspool`) VALUES
(1, 'yes', '1.0GB Uploaded', '275.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1073741824, '0.0'),
(2, 'yes', '2.5GB Uploaded', '350.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 2684354560, '0.0'),
(3, 'yes', '5GB Uploaded', '550.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 5368709120, '0.0'),
(4, 'yes', '3 Invites', '1000.0', 'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.', 'invite', 3, '0.0'),
(5, 'yes', 'Custom Title!', '500.0', 'For only 500 Bonus Points, you can buy yourself a custom title. The only restrictions are no foul or offensive language or user class can be entered. The points are then removed from your Bonus Bank and your special title is changed to the title of your choice', 'title', 1, '0.0'),
(6, 'yes', 'VIP Status', '5000.0', 'With enough bonus points acquired, you can buy yourself VIP status for one month. The points are then removed from your Bonus Bank and your status is changed.', 'class', 1, '0.0'),
(7, 'yes', 'Give A Karma Gift', '100.0', 'Well perhaps you don''t need the upload credit, but you know somebody that could use the Karma boost! You are now able to give your Karma credits as  a gift! The points are then removed from your Bonus Bank and  added to the account of a user of your choice!\r\n\r\nAnd they recieve a PM with all the info as well as who it came from...', 'gift_1', 1073741824, '0.0'),
(8, 'yes', 'Custom Smilies', '300.0', 'With enough bonus points acquired, you can buy yourself a set of custom smilies for one month! The points are then removed from your Bonus Bank and with a click of a link, your new smilies are available whenever you post or comment!', 'smile', 1, '0.0'),
(9, 'yes', 'Remove Warning', '1000.0', 'With enough bonus points acquired... So you''ve been naughty... tsk tsk.. Yep now for only 1000 points you can have that warning taken away !', 'warning', 1, '0.0'),
(10, 'yes', 'Ratio Fix', '500.0', 'With enough bonus points acquired, you can bring the ratio of one torrent to a 1 to 1 ratio! The points are then removed from your Bonus Bank and your status is changed.', 'ratio', 1, '0.0'),
(11, 'yes', '3 Freeleech Slots', '1000.0', 'With enough bonus points acquired, you are able to exchange them for some Freeleech Slots. The points are then removed from your Bonus Bank and the slots are added to your free slots amount.', 'freeslots', 3, '0.0'),
(12, 'yes', '10GB Uploaded', '1000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 10737418240, '0.0'),
(13, 'yes', '25GB Uploaded', '2000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 26843545600, '0.0'),
(14, 'yes', '50GB Uploaded', '4000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 53687091200, '0.0'),
(15, 'yes', '100GB Uploaded', '8000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 107374182400, '0.0'),
(16, 'yes', '520GB Uploaded', '40000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 558345748480, '0.0'),
(17, 'yes', '1TB Uploaded', '80000.0', 'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.', 'traffic', 1116691496960, '0.0'),
(18, 'yes', '200 Bonus Points - Invite trade-in', '1.0', 'If you have 1 invite and don''t use them click the button to trade them in for 200 Bonus Points.', 'itrade', 200, '0.0'),
(19, 'yes', 'Freeslots - Invite trade-in', '1.0', 'If you have 1 invite and don''t use them click the button to trade them in for 2 Free Slots.', 'itrade2', 2, '0.0');

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  `private` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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

INSERT INTO `cards` (`id`, `points`, `pic`) VALUES
(1, 2, '2p.bmp'),
(2, 3, '3p.bmp'),
(3, 4, '4p.bmp'),
(4, 5, '5p.bmp'),
(5, 6, '6p.bmp'),
(6, 7, '7p.bmp'),
(7, 8, '8p.bmp'),
(8, 9, '9p.bmp'),
(9, 10, '10p.bmp'),
(10, 10, 'vp.bmp'),
(11, 10, 'dp.bmp'),
(12, 10, 'kp.bmp'),
(13, 1, 'tp.bmp'),
(14, 2, '2b.bmp'),
(15, 3, '3b.bmp'),
(16, 4, '4b.bmp'),
(17, 5, '5b.bmp'),
(18, 6, '6b.bmp'),
(19, 7, '7b.bmp'),
(20, 8, '8b.bmp'),
(21, 9, '9b.bmp'),
(22, 10, '10b.bmp'),
(23, 10, 'vb.bmp'),
(24, 10, 'db.bmp'),
(25, 10, 'kb.bmp'),
(26, 1, 'tb.bmp'),
(27, 2, '2k.bmp'),
(28, 3, '3k.bmp'),
(29, 4, '4k.bmp'),
(30, 5, '5k.bmp'),
(31, 6, '6k.bmp'),
(32, 7, '7k.bmp'),
(33, 8, '8k.bmp'),
(34, 9, '9k.bmp'),
(35, 10, '10k.bmp'),
(36, 10, 'vk.bmp'),
(37, 10, 'dk.bmp'),
(38, 10, 'kk.bmp'),
(39, 1, 'tk.bmp'),
(40, 2, '2c.bmp'),
(41, 3, '3c.bmp'),
(42, 4, '4c.bmp'),
(43, 5, '5c.bmp'),
(44, 6, '6c.bmp'),
(45, 7, '7c.bmp'),
(46, 8, '8c.bmp'),
(47, 9, '9c.bmp'),
(48, 10, '10c.bmp'),
(49, 10, 'vc.bmp'),
(50, 10, 'dc.bmp'),
(51, 10, 'kc.bmp'),
(52, 1, 'tc.bmp');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

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

INSERT INTO `categories` (`id`, `name`, `image`) VALUES
(1, 'Appz/PC ISO', 'cat_apps.gif'),
(2, 'Games/PC ISO', 'cat_games.gif'),
(3, 'Movies/SVCD', 'cat_movies.gif'),
(4, 'Music', 'cat_music.gif'),
(5, 'Episodes', 'cat_episodes.gif'),
(6, 'XXX', 'cat_xxx.gif'),
(7, 'Games/GBA', 'cat_games.gif'),
(8, 'Games/PS2', 'cat_games.gif'),
(9, 'Anime', 'cat_anime.gif'),
(10, 'Movies/XviD', 'cat_movies.gif'),
(11, 'Movies/DVD-R', 'cat_movies.gif'),
(12, 'Games/PC Rips', 'cat_games.gif'),
(13, 'Appz/misc', 'cat_apps.gif');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `changelog`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
  `comment_history` mediumtext collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

INSERT INTO `countries` (`id`, `name`, `flagpic`) VALUES
(1, 'Sweden', 'sweden.gif'),
(2, 'United States of America', 'usa.gif'),
(3, 'Russia', 'russia.gif'),
(4, 'Finland', 'finland.gif'),
(5, 'Canada', 'canada.gif'),
(6, 'France', 'france.gif'),
(7, 'Germany', 'germany.gif'),
(8, 'China', 'china.gif'),
(9, 'Italy', 'italy.gif'),
(10, 'Denmark', 'denmark.gif'),
(11, 'Norway', 'norway.gif'),
(12, 'United Kingdom', 'uk.gif'),
(13, 'Ireland', 'ireland.gif'),
(14, 'Poland', 'poland.gif'),
(15, 'Netherlands', 'netherlands.gif'),
(16, 'Belgium', 'belgium.gif'),
(17, 'Japan', 'japan.gif'),
(18, 'Brazil', 'brazil.gif'),
(19, 'Argentina', 'argentina.gif'),
(20, 'Australia', 'australia.gif'),
(21, 'New Zealand', 'newzealand.gif'),
(23, 'Spain', 'spain.gif'),
(24, 'Portugal', 'portugal.gif'),
(25, 'Mexico', 'mexico.gif'),
(26, 'Singapore', 'singapore.gif'),
(27, 'India', 'india.gif'),
(28, 'Albania', 'albania.gif'),
(29, 'South Africa', 'southafrica.gif'),
(30, 'South Korea', 'southkorea.gif'),
(31, 'Jamaica', 'jamaica.gif'),
(32, 'Luxembourg', 'luxembourg.gif'),
(33, 'Hong Kong', 'hongkong.gif'),
(34, 'Belize', 'belize.gif'),
(35, 'Algeria', 'algeria.gif'),
(36, 'Angola', 'angola.gif'),
(37, 'Austria', 'austria.gif'),
(38, 'Yugoslavia', 'yugoslavia.gif'),
(39, 'Western Samoa', 'westernsamoa.gif'),
(40, 'Malaysia', 'malaysia.gif'),
(41, 'Dominican Republic', 'dominicanrep.gif'),
(42, 'Greece', 'greece.gif'),
(43, 'Guatemala', 'guatemala.gif'),
(44, 'Israel', 'israel.gif'),
(45, 'Pakistan', 'pakistan.gif'),
(46, 'Czech Republic', 'czechrep.gif'),
(47, 'Serbia', 'serbia.gif'),
(48, 'Seychelles', 'seychelles.gif'),
(49, 'Taiwan', 'taiwan.gif'),
(50, 'Puerto Rico', 'puertorico.gif'),
(51, 'Chile', 'chile.gif'),
(52, 'Cuba', 'cuba.gif'),
(53, 'Congo', 'congo.gif'),
(54, 'Afghanistan', 'afghanistan.gif'),
(55, 'Turkey', 'turkey.gif'),
(56, 'Uzbekistan', 'uzbekistan.gif'),
(57, 'Switzerland', 'switzerland.gif'),
(58, 'Kiribati', 'kiribati.gif'),
(59, 'Philippines', 'philippines.gif'),
(60, 'Burkina Faso', 'burkinafaso.gif'),
(61, 'Nigeria', 'nigeria.gif'),
(62, 'Iceland', 'iceland.gif'),
(63, 'Nauru', 'nauru.gif'),
(64, 'Slovenia', 'slovenia.gif'),
(66, 'Turkmenistan', 'turkmenistan.gif'),
(67, 'Bosnia Herzegovina', 'bosniaherzegovina.gif'),
(68, 'Andorra', 'andorra.gif'),
(69, 'Lithuania', 'lithuania.gif'),
(70, 'Macedonia', 'macedonia.gif'),
(71, 'Netherlands Antilles', 'nethantilles.gif'),
(72, 'Ukraine', 'ukraine.gif'),
(73, 'Venezuela', 'venezuela.gif'),
(74, 'Hungary', 'hungary.gif'),
(75, 'Romania', 'romania.gif'),
(76, 'Vanuatu', 'vanuatu.gif'),
(77, 'Vietnam', 'vietnam.gif'),
(78, 'Trinidad & Tobago', 'trinidadandtobago.gif'),
(79, 'Honduras', 'honduras.gif'),
(80, 'Kyrgyzstan', 'kyrgyzstan.gif'),
(81, 'Ecuador', 'ecuador.gif'),
(82, 'Bahamas', 'bahamas.gif'),
(83, 'Peru', 'peru.gif'),
(84, 'Cambodia', 'cambodia.gif'),
(85, 'Barbados', 'barbados.gif'),
(86, 'Bangladesh', 'bangladesh.gif'),
(87, 'Laos', 'laos.gif'),
(88, 'Uruguay', 'uruguay.gif'),
(89, 'Antigua Barbuda', 'antiguabarbuda.gif'),
(90, 'Paraguay', 'paraguay.gif'),
(93, 'Thailand', 'thailand.gif'),
(92, 'Union of Soviet Socialist Republics', 'ussr.gif'),
(94, 'Senegal', 'senegal.gif'),
(95, 'Togo', 'togo.gif'),
(96, 'North Korea', 'northkorea.gif'),
(97, 'Croatia', 'croatia.gif'),
(98, 'Estonia', 'estonia.gif'),
(99, 'Colombia', 'colombia.gif'),
(100, 'Lebanon', 'lebanon.gif'),
(101, 'Latvia', 'latvia.gif'),
(102, 'Costa Rica', 'costarica.gif'),
(103, 'Egypt', 'egypt.gif'),
(104, 'Bulgaria', 'bulgaria.gif'),
(105, 'Isla de Muerte', 'jollyroger.gif'),
(106, 'Scotland', 'scotland.gif');

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

INSERT INTO `delete_hr` (`delete_date`) VALUES
('2008-09-25 20:31:36');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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

INSERT INTO `faq` (`id`, `cid`, `question`, `answer`, `ctime`, `mtime`) VALUES
(1, 1, 'What is this bittorrent all about anyway? How do I get the files?', 'Check out Brian''s BitTorrent FAQ and Guide.', 1162676179, 1162844691),
(2, 2, 'Where does the donated money go?', 'yoursite is situated on a dedicated server in the Netherlands. For the moment we have monthly running costs of approximately ', 1162868062, 1162406569),
(3, 1, 'Where can I get a copy of the source code?', 'Its available @ http://tbdev.net ...Its open  free source code..... Made by the People For The People...', 1162676179, 1225736457),
(4, 2, 'I registered an account but did not receive the confirmation e-mail!', 'You can use this form to delete the account so you can re-register. Note though that if you didn''t receive the email the first time it will probably not succeed the second time either so you should really try another email address.', 1162676179, 1162406569),
(5, 2, 'I have lost my user name or password! Can you send it to me?', 'Please use this form to have the login details mailed back to you.', 1162868062, 1162406569),
(6, 2, 'Can you rename my account?', 'We do not rename accounts. Please create a new one. (Use this form to delete your present account.)', 1162676179, 1162406569),
(7, 2, 'Can you delete my (confirmed) account?', 'You can do it yourself by using this form', 1162676179, 1162406569),
(8, 2, 'So, what''s MY ratio?', 'Click on your profile, then on your user name (at the top).\r\n\r\nIt''s important to distinguish between your overall ratio and the individual ratio on each torrent you may be seeding or leeching. The overall ratio takes into account the total uploaded and downloaded from your account since you joined the site. The individual ratio takes into account those values for each torrent.\r\n\r\nYou may see two symbols instead of a number: "Inf.", which is just an abbreviation for Infinity, and means that you have downloaded 0 bytes while uploading a non-zero amount (ul/dl becomes infinity); "---", which should be read as "non-available", and shows up when you have both downloaded and uploaded 0 bytes (ul/dl = 0/0 which is an indeterminate amount).', 1162868062, 1162651503),
(9, 2, 'Why is my IP displayed on my details page?', 'Only you and the site moderators can view your IP address and email. Regular users do not see that information.', 1162676179, 1162406569),
(10, 2, 'Help! I cannot login!? (a.k.a. Login of Death)', 'This problem sometimes occurs with MSIE. Close all Internet Explorer windows and open Internet Options in the control panel. Click the Delete Cookies button. You should now be able to login.', 1162868062, 1162406569),
(11, 2, 'My IP address is dynamic. How do I stay logged in?', 'You do not have to anymore. All you have to do is make sure you are logged in with your actual IP when starting a torrent session. After that, even if the IP changes mid-session, the seeding or leeching will continue and the statistics will update without any problem.', 1162676179, 1162406569),
(12, 2, 'Why am I listed as not connectable? (And why should I care?)', 'The tracker has determined that you are firewalled or NATed and cannot accept incoming connections.\r\n\r\nThis means that other peers in the swarm will be unable to connect to you, only you to them. Even worse, if two peers are both in this state they will not be able to connect at all. This has obviously a detrimental effect on the overall speed.\r\n\r\nThe way to solve the problem involves opening the ports used for incoming connections (the same range you defined in your client) on the firewall and/or configuring your NAT server to use a basic form of NAT for that range instead of NAPT (the actual process differs widely between different router models. Check your router documentation and/or support forum. You will also find lots of information on the subject at PortForward).', 1162676179, 1162406569),
(13, 2, 'What are the different user classes?', 'User The default class of new members.\r\n Power User Can download DOX over 1MB and view NFO files.\r\n Star Has donated money to TorrentBits.org .\r\n VIP Same privileges as Power User and is considered an Elite Member of TorrentBits. Immune to automatic demotion.\r\n Other Customised title.\r\n Uploader Same as PU except with upload rights and immune to automatic demotion.\r\n Moderator Can edit and delete any uploaded torrents. Can also moderate user comments and disable accounts.\r\n Administrator Can do just about anything.\r\n SysOp Redbeard (site owner).', 1162676179, 1162406569),
(14, 2, 'How does this promotion thing work anyway?', 'Power User Must have been be a member for at least 4 weeks, have uploaded at least 25GB and have a ratio at or above 1.05.\r\nThe promotion is automatic when these conditions are met. Note that you will be automatically demoted from\r\nthis status if your ratio drops below 0.95 at any time.\r\n Star Just donate, and send Redbeard - and only Redbeard - the details.\r\n VIP Assigned by mods at their discretion to users they feel contribute something special to the site.\r\n(Anyone begging for VIP status will be automatically disqualified.)\r\n Other Conferred by mods at their discretion (not available to Users or Power Users).\r\n Uploader Appointed by Admins/SysOp (see the ''Uploading'' section for conditions).\r\n Moderator You don''t ask us, we''ll ask you!', 1162676179, 1162406569),
(15, 2, 'Hey! I''ve seen Power Users with less than 25GB uploaded!', 'The PU limit used to be 10GB and we didn''t demote anyone when we raised it to 25GB', 1162676179, 1162406569),
(16, 2, 'Why can''t my friend become a member?', 'There is a 75.000 users limit. When that number is reached we stop accepting new members. Accounts inactive for more than 42 days are automatically deleted, so keep trying. (There is no reservation or queuing system, don''t ask for that.)', 1162676179, 1162406569),
(17, 2, 'How do I add an avatar to my profile?', 'First, find an image that you like, and that is within the rules. Then you will have to find a place to host it, such as our own BitBucket. (Other popular choices are Photobucket, Upload-It! or ImageShack). All that is left to do is copy the URL you were given when uploading it to the avatar field in your profile.\r\n\r\nPlease do not make a post just to test your avatar. If everything is allright you''ll see it in your details page. ', 1162676179, 1162406569),
(18, 3, 'Most common reason for stats not updating', ' * The user is cheating. (a.k.a. "Summary Ban")\r\n * The server is overloaded and unresponsive. Just try to keep the session open until the server responds again. (Flooding the server with consecutive manual updates is not recommended.)\r\n * You are using a faulty client. If you want to use an experimental or CVS version you do it at your own risk.\r\n', 1162676179, 1162406569),
(19, 3, 'Best practices', ' * If a torrent you are currently leeching/seeding is not listed on your profile, just wait or force a manual update.\r\n * Make sure you exit your client properly, so that the tracker receives "event=completed".\r\n * If the tracker is down, do not stop seeding. As long as the tracker is back up before you exit the client the stats should update properly.', 1162676179, 1162406569),
(20, 5, 'How do I use the files I''ve downloaded?', 'Check out this guide http://localhost/TBDEV/formats.php', 1162676179, 1162767711),
(21, 3, 'May I use any bittorrent client?', 'Yes. The tracker now updates the stats correctly for all bittorrent clients. However, we still recommend that you avoid the following clients:\r\n\r\nï¿½ BitTorrent++,\r\nï¿½ Nova Torrent,\r\nï¿½ TorrentStorm.\r\n\r\nThese clients do not report correctly to the tracker when canceling/finishing a torrent session. If you use them then a few MB may not be counted towards the stats near the end, and torrents may still be listed in your profile for some time after you have closed the client.\r\n\r\nAlso, clients in alpha or beta version should be avoided.', 1162676179, 1162768097),
(22, 3, 'Why is a torrent I''m leeching/seeding listed several times in my profile?', 'If for some reason (e.g. pc crash, or frozen client) your client exits improperly and you restart it, it will have a new peer_id, so it will show as a new torrent. The old one will never receive a "event=completed" or "event=stopped" and will be listed until some tracker timeout. Just ignore it, it will eventually go away.', 1162676179, 1162768208),
(23, 8, 'Maybe my address is blacklisted?', 'The site blocks addresses listed in the (former) PeerGuardian database, as well as addresses of banned users. This works at Apache/PHP level, it''s just a script that blocks logins from those addresses. It should not stop you from reaching the site. In particular it does not block lower level protocols, you should be able to ping/traceroute the server even if your address is blacklisted. If you cannot then the reason for the problem lies elsewhere.\r\n\r\nIf somehow your address is indeed blocked in the PG database do not contact us about it, it is not our policy to open ad hoc exceptions. You should clear your IP with the database maintainers instead.', 1163155783, 1162820705),
(24, 8, 'My ISP blocks the site''s address', '(In first place, it''s unlikely your ISP is doing so. DNS name resolution and/or network problems are the usual culprits.)\r\nThere''s nothing we can do. You should contact your ISP (or get a new one). Note that you can still visit the site via a proxy, follow the instructions in the relevant section. In this case it doesn''t matter if the proxy is anonymous or not, or which port it listens to.\r\n\r\nNotice that you will always be listed as an "unconnectable" client because the tracker will be unable to check that you''re capable of accepting incoming connections.', 1163155783, 1162821075),
(25, 8, 'Is there an alternate port (81)?', 'Some of our torrents use ports other than the usual HTTP port 80. This may cause problems for some users, for instance those behind some firewall or proxy configurations. You can easily solve this by editing the .torrent file yourself with any torrent editor, e.g. MakeTorrent, and replacing the announce url torrentbits.org:81 with torrentbits.org:80 or just torrentbits.org.\r\n\r\nEditing the .torrent with Notepad is not recommended. It may look like a text file, but it is in fact a bencoded file. If for some reason you must use a plain text editor, change the announce url to torrentbits.org:80, not torrentbits.org. (If you''re thinking about changing the number before the announce url instead, you know too much to be reading this.)', 1163155783, 1162821157),
(27, 5, 'Downloaded a movie and don''t know what CAM/TS/TC/SCR means?', 'Check out this guide.', 1163165698, 0),
(28, 5, 'Why did an active torrent suddenly disappear?', 'There may be three reasons for this:\r\n(1) The torrent may have been out-of-sync with the site rules.\r\n(2) The uploader may have deleted it because it was a bad release. A replacement will probably be uploaded to take its place.\r\n(3) Torrents are automatically deleted after 28 days.', 1163166050, 0),
(29, 9, 'What if my Question isn''t answered here?', 'Post in the Forums, by all means. You''ll find they are usually a friendly and helpful place, provided you follow a few basic guidelines:\r\n\r\n * Make sure your problem is not really in this FAQ. There''s no point in posting just to be sent back here.\r\n * Before posting read the sticky topics (the ones at the top). Many times new information that still hasn''t been incorporated in the FAQ can be found there.\r\n * Help us in helping you. Do not just say "it doesn''t work!". Provide details so that we don''t have to guess or waste time asking. What client do you use? What''s your OS? What''s your network setup? What''s the exact error message you get, if any? What are the torrents you are having problems with? The more you tell the easiest it will be for us, and the more probable your post will get a reply.\r\n * And needless to say: be polite. Demanding help rarely works, asking for it usually does the trick.', 1163168322, 0),
(30, 9, 'What if I find a rabbit on the tracker?', 'Roadkill! :D', 1163168717, 1162911763);

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

INSERT INTO `faq_categories` (`cid`, `fcat_name`) VALUES
(1, 'Site Information'),
(2, 'User Information'),
(3, 'Stats'),
(4, 'Uploading'),
(5, 'Downloading'),
(6, 'How can I improve my download speed?'),
(7, 'My ISP uses a transparent proxy. What should I do?'),
(8, 'Why can''t I connect? Is the site blocking me?'),
(9, 'Miscellaneous');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `forums`
--

-- --------------------------------------------------------

--
-- Table structure for table `freepoll`
--

CREATE TABLE `freepoll` (
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `freeslots`
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

INSERT INTO `free_download` (`free`, `free_for_all`, `title`, `message`) VALUES
('free', 'yes', 'test', 'test');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `friends`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends2`
--

CREATE TABLE `friends2` (
  `user` int(10) unsigned NOT NULL default '0',
  `friend` int(10) unsigned NOT NULL default '0',
  `type` enum('friend','block') NOT NULL default 'friend',
  KEY `user_type` (`user`,`type`),
  KEY `user_friend` (`user`,`friend`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friends2`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

INSERT INTO `gallery_admin` (`per_page`, `num_rows`, `max_file_size`) VALUES
(20, 5, 402880);

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

INSERT INTO `gallery_admin_users` (`user_class`, `gal_per_member`, `number_total`, `number_of_pics`) VALUES
(0, 0, 0, 0),
(1, 2, 20, 1),
(2, 4, 30, 4),
(3, 6, 40, 6),
(4, 8, 50, 8),
(5, 10, 60, 10),
(6, 12, 100, 10),
(7, 12, 100, 10);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `happylog`
--

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk`
--

CREATE TABLE `helpdesk` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `closed` enum('yes','no') NOT NULL default 'no',
  `message` varchar(255) NOT NULL default '',
  `ticket` int(15) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `read_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `userid` int(11) NOT NULL default '0',
  `edit_by` int(11) NOT NULL default '0',
  `edit_date` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

INSERT INTO `links_categories` (`cid`, `rcat_name`, `min_class_read`) VALUES
(1, 'Link to us!', 0),
(2, 'Site Links', 0),
(3, 'Other Links', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `subject` text,
  `msg` text,
  `unread` enum('yes','no') NOT NULL default 'yes',
  `poster` bigint(20) unsigned NOT NULL default '0',
  `location` enum('in','out','both','draft','template') NOT NULL default 'in',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`),
  KEY `receiver_location` (`receiver`,`location`),
  KEY `sender_location` (`sender`,`location`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

INSERT INTO `overforums` (`id`, `name`, `description`, `minclassview`, `forid`, `sort`) VALUES
(1, 'Announcements', 'All Site Announcements and News', 0, 0, 0),
(2, 'Off Topic', 'As The Title Says !', 0, 0, 1),
(3, 'Audio/Video', 'All Audio And Video Posts Here', 0, 0, 2),
(4, 'Games', 'Online Gamers - Home Pc Gamers - X360 Gamers Here', 0, 0, 3),
(5, 'Pc', 'All Pc Related Issue''s Here', 0, 0, 4),
(6, 'Help', 'Site Help , Bug Reports', 0, 0, 5),
(7, 'Staff Forums', 'All Staff Forums Here', 4, 1, 6);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `photo_gallery`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `poller`
--

INSERT INTO `poller` (`ID`, `pollerTitle`) VALUES
(1, 'How would you rate this script?'),
(2, 'Testing Ajax Poller'),
(3, 'What pet do you have?'),
(4, 'Are you waiting for Santa?');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `poller_option`
--

INSERT INTO `poller_option` (`ID`, `pollerID`, `optionText`, `pollerOrder`, `defaultChecked`) VALUES
(1, 1, 'Excellent', 1, '1'),
(2, 1, 'Very good', 2, '0'),
(3, 1, 'Good', 3, '0'),
(4, 1, 'Fair', 3, '0'),
(5, 1, 'Poor', 4, '0'),
(6, 2, 'Better than the default poll ', 0, '0'),
(7, 2, 'its the shit', 1, '0'),
(8, 2, 'dont give a fcuk', 2, '0'),
(9, 3, 'Cat', 0, '0'),
(10, 3, 'Dog', 1, '0'),
(11, 3, 'Birds', 2, '0'),
(12, 3, 'Monkey', 3, '0'),
(13, 3, 'Fish', 4, '0'),
(14, 3, 'No pet', 5, '0'),
(15, 4, 'Yes', 0, '0'),
(16, 4, 'No ', 1, '0'),
(17, 4, 'I have been bad so he will not come :(', 2, '0'),
(18, 4, 'Who is Santa?', 3, '0');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `polls`
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
  `body` text collate utf8_bin,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `post_history` mediumtext collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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

INSERT INTO `rules` (`id`, `cid`, `heading`, `body`, `ctime`, `mtime`) VALUES
(1, 1, ':: ::General rules - Breaking these rules can and will get you banned !', 'Access to the newest torrents is conditional on a good ratio! (See the FAQ  for details.)\r\nLow ratios may result in severe consequences, including banning in extreme cases.\r\n\r\nGeneral Guidelines - Please follow these guidelines or else you might end up with a warning!\r\n\r\nNo duplicate accounts from the same IP.   Members with more than one account for whatever reason without approval of Sysops or staff leaders will get banned  please do not make multiple  accounts!.\r\nNo aggressive behavior or flaming in the forums.\r\nNo trashing of other peoples topics (e.g. SPAM)\r\nNo language other than English in the forums.\r\n\r\nThis site has very strict rules in regards to racial slurs and racist remarks', 1214338879, 1214079783),
(2, 2, ':: ::Forum Rules', ' # Please, feel free to answer any questions but leave the moderating to the moderators.\r\n     # Don''t use all capital letters, excessive !!! (exclamation marks) or ??? (question marks)... it seems like you''re shouting.\r\n     # No posting of users stats without their consent is allowed in the forums or torrent comments regardless of ratio or class.  \r\n     # No trashing of other peoples topics.\r\n     # No systematic foul language (and none at all on titles).\r\n     # No double posting. If you wish to post again, and yours is the last post in the thread please use the EDIT function, instead of posting a double.    \r\n     # No bumping... (All bumped threads will be Locked.)  \r\n     # No direct links to internet sites in the forums.      \r\n     # No images larger than 400x400, and preferably web-optimised. Use the [imgw] tag for larger images.\r\n     # No advertising, mechandising or promotions of any sort are allowed on the site.    \r\n     # Do not tell people to read the Rules, the FAQ, or comment on their ratios and torrents.    \r\n     # No consistent off-topic posts allowed in the forums. (i.e. SPAM or hijacking)  \r\n     # The Trading/Requesting of invites to other sites is forbidden in the forums.  \r\n     # Do not post links to other torrent sites or torrents on those sites.    \r\n     # Users are not allowed, under any circumstance to create their own polls in the forum.    \r\n     # No self-congratulatory topics are allowed in the forums.    \r\n     # Do not quote excessively. One quote of a quote box is sufficient.    \r\n     # Please ensure all questions are posted in the correct section!     (Game questions in the Games section, Apps questions in the Apps section, etc.)    \r\n     # Please, feel free to answer any questions.. However remain respectful to the people you help ....nobodys better than anyone else.    \r\n     # Last, please read the FAQ before asking any question', 1214339023, 0),
(3, 4, ':: ::Uploaders Rules', 'All uploaders are subject to follow the below rules in order to be a part of the  uploader team. We realize that it''s quite a list, and for new uploaders, it might seem a bit overwhelming, but as you spend time here, they''ll become second hat.\r\n\r\nTo apply to become a site uploader use the uploaders application form, contact staff to get the link.\r\n\r\nTorrents that do not follow the rules below will be deleted.  If you have any questions about the below rules, please feel free to PM them and I will clarify as best I can.\r\n\r\nWelcome to the team and happy uploading!\r\n\r\n# All Uploaders must upload a minimum of 3 unique torrents each week to retain their Uploader status.  Failure to comply will result in a demotion, and a minimum of a 2 week blackout period where they will not be able to return to the Uploader team.  If, after the 2 weeks, the Uploader can prove they will be active, they will be reinstated.  A second instance of inactivity will be cause for permanent removal from the Uploader team.  Extenuating circumstances will be considered if it is the cause of inactivity.  If you are going to be away, please let a staff member know so that your account is not affected.\r\n# All torrents must be rarred, no matter what the size or type.  The ONLY exception to this is MP3s.  Guidelines for rarring your own releases are as follows:\r\n', 1214339203, 0),
(4, 5, ':: ::Free leech rules', '      From time to time we will have freeleech for 48hours. This means that when you download from site it will not count against your download ratio.\r\n\r\n      Whatever you seed back will add to your upload ratio.\r\n\r\nThis is a good opportunity for members with ratio''s below 1.0 to bring them back into line\r\n\r\nAnyone who hit and runs on a freeleech torrent will receive a mandatory 2 week warning. You must seed all torrents downloaded to  100% or for a minimum of 48 hours this is for free leech torrents only.\r\n\r\n', 1214339464, 0),
(5, 6, ':: ::Downloading rules', 'No comments on torrents you are not about to download\r\nOnce download is complete, remember to seed for as long as possible or for a minimum of 36 hours or a ratio of 1:1\r\nLow ratios will be given the three strike warning from staff and can lead to a total ban', 1214339531, 0);

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

INSERT INTO `rules_categories` (`cid`, `rcat_name`, `min_class_read`) VALUES
(1, ':: ::General Site Rules', 0),
(2, ':: ::Forum Rules', 0),
(5, ':: ::Free leech rules', 0),
(3, ':: ::Uploaders Rules', 0),
(6, ':: ::Downloading rules', 0);

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

INSERT INTO `siteonline` (`onoff`, `reason`, `class`, `class_name`) VALUES
(1, 'Server Offline For Updates - Back Soon !', 0, 'just for User');

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
  `timesann` int(10) unsigned NOT NULL default '0',
  `hit_run` int(2) default '0',
  `prewarn` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished` enum('yes','no') NOT NULL default 'no',
  `sl_warned` enum('yes','no') NOT NULL default 'no',
  `torrent_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tr_usr` (`torrentid`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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

INSERT INTO `staffpanel` (`id`, `page_name`, `file_name`, `description`, `av_class`, `added_by`, `added`) VALUES
(1, 'Ratio Edit', 'ratioedit', 'Adjust Members Ratio Here', 6, 1, 1217836568),
(2, 'Optimise Database', 'mysql_overview', 'View And Optimise The Database From The Tracker', 7, 1, 1217836655),
(3, 'Uploader Applications', 'uploadapps', 'View All New Uploader Applications', 6, 1, 1217836885),
(4, 'Manage Chmod', 'chmod', 'Manage Ftp Directory Chmod Permissions', 7, 1, 1217836942),
(5, 'Manual Clean up', 'docleanup', 'Run Site Clean Up ', 6, 1, 1217837040),
(6, 'Mass Bonus', 'massbonus', 'Award All Members 500 Karma Points', 6, 1, 1217837358),
(7, 'Cache Stylesheets', 'cachestylesheets', 'Cache The StyleSheets', 6, 1, 1217837552),
(8, 'Ip Bans', 'bans', 'Ip Ban Bad Users', 6, 1, 1217838226),
(9, 'Donations', 'donations', 'View All Site Donators And Donations Here', 6, 1, 1217838383),
(10, 'Site Check', 'sitecheck', 'Coders Site Checker', 7, 1, 1217838638),
(11, 'Hack Log', 'hacklog', 'Monitor Possible Xss Hack Attempts', 6, 1, 1217838737),
(12, 'Client Viewer', 'detectclients', 'View Clients Peer Id For Banning Clients', 6, 1, 1217838860),
(13, 'Db Admin', 'msgspy', 'Delete And Manage Spam', 6, 1, 1217839090),
(14, 'Upload Bonus', 'upload-bonus', 'Award All Members Upload Bonus', 6, 1, 1217839153),
(15, 'Slots Manager', 'manage-slots', 'Manage Members Freeslots', 6, 1, 1217839273),
(16, 'Delete User', 'delacctadmin', 'Delete Members Accounts', 5, 1, 1217839394),
(17, 'Check Torrent Comments', 'torrentcomments', 'Check Comments For Karma Whores', 5, 1, 1217839483),
(18, 'Mass Pm', 'masspm', 'Mass Pm All Members', 5, 1, 1217840638),
(19, 'View Reports', 'reports', 'View All Site Reports', 5, 1, 1217840706),
(20, 'Flush Log', 'flush_log', 'View All Members Flushes', 5, 1, 1217841092),
(21, 'Announcements', 'usersearch', 'Create New Announcement', 5, 1, 1217843007),
(22, 'Banned Clients', 'client_clearban', 'View Banned And Ban Clients', 5, 1, 1217843161),
(23, 'Invite Manager', 'inviteadd', 'Manage Members Invites', 5, 1, 1217843289),
(24, 'Reset ShoutBox', 'resetshoutbox', 'Clear Old Shout Box History', 5, 1, 1217853919),
(25, 'Edit Faq', 'faqadmin', 'Edit Site Faq Categories', 5, 1, 1217854009),
(27, 'Bonus Manager', 'bonusmanage', 'Manage Site Bonus Options', 5, 1, 1217854158),
(28, 'Add User', 'adduser', 'Manually Create A New Account', 5, 1, 1217854252),
(29, 'Advanced Account Manager', 'acpmanage', 'Manage Bans - Disabled - Pending Users', 5, 1, 1217854481),
(30, 'Reset Banned', 'maxlogin', 'Reset Banned Failed Login Attempts', 5, 1, 1217854628),
(31, 'Edit Rules', 'rules_admin', 'Edit Site Rules', 5, 1, 1217856210),
(33, 'Edit Links', 'links_admin', 'Edit Site Links', 5, 1, 1217856548),
(34, 'Warned User''s', 'warned', 'Manage Warned Users', 4, 1, 1217861230),
(35, 'Rip Nfo', 'nforipper', 'Rip Ascii From Nfo''s', 4, 1, 1217861313),
(36, 'Invited Users', 'invitedby', 'Show All Invited Users', 4, 1, 1217861373),
(37, 'Uploader Activity', 'stats', 'View Uploader Activity And Categorie Activity', 4, 1, 1217861859),
(38, 'Category Manager', 'categorie', 'Manage Site Categories', 4, 1, 1217861924),
(39, 'Name Changer', 'namechanger', 'Change Members Nicks', 4, 1, 1217862003),
(40, 'Site Log', 'log', 'View All Site log Entrys', 4, 1, 1217862053),
(41, 'User List', 'users', 'Full Site User List', 4, 1, 1217862162),
(42, 'Forum Manager', 'forummanage', 'Manage And Edit Forums', 4, 1, 1217862210),
(43, 'Inactive Users', 'inactive', 'Show All Inactive Members - Notify By Email', 4, 1, 1217862406),
(44, 'Reset Password', 'reset', 'Reset Forgotten Passwords', 4, 1, 1217862448),
(45, 'Snatched Torrents', 'snatched_torrents', 'View All Site Snatches', 4, 1, 1217862509),
(46, 'Duplicate Ip''s', 'ipcheck', 'Check Site For Duplicate Ip''s', 4, 1, 1217862581),
(47, 'Not Connectable', 'findnotconnectable', 'View All Non-Connectable Members', 4, 1, 1217862663),
(48, 'Site Peers', 'viewpeers', 'View All Site Peers', 4, 1, 1217862722),
(49, 'Free Leech', 'freeleech', 'Free Leech For All', 5, 1, 1217886796),
(50, 'Advanced User Search', 'usersearch1', 'Carry Out Advanced User Searches', 4, 1, 1218321784),
(51, 'HtAccessor', 'htaccesser', 'Make .Htaccess files', 7, 1, 1218993346),
(53, 'Check Invites', 'invitesinplay', 'Keep Tabs on invite''s ', 6, 1, 1219524702),
(55, 'Advanced Db Manager', 'database', 'Back up, Check, Repair, Optimize Db', 7, 1, 1219602892),
(56, 'Cache Countries', 'cachecountries', 'Cache Countries When Adding New Entrys', 6, 1, 1219664241),
(57, 'Cache Categories', 'cachecategories', 'Cache Catigories When Adding New Entry', 6, 1, 1219664338),
(58, 'View Shout History', 'shistory', 'Shout History Check', 6, 1, 1220057674),
(59, 'Edit Php Files', 'editfiles', 'Only Use This If You Know What Your Doing', 7, 1, 1220104662),
(60, 'Php File Edit Log', 'editlog', 'Coders Php File Edit Log', 7, 1, 1220104851),
(61, 'Sql Query Script', 'sqlcmdex', 'Execute Raw Sql Commands From Tracker', 7, 1, 1220181900),
(62, 'Users Possibilities', 'userspos', 'View User Settings', 4, 1, 1220640589),
(63, 'PhpInfo', 'system_view', 'PhpInfo - Check Filepaths And Configs', 7, 1, 1220781999),
(64, 'Byte Calculator', 'calculator', 'Convert gigbytes to bytes ect for ratio adjustments', 4, 1, 1221381023),
(65, 'Advanced Bonus Manager', 'bonuspoints', 'New Advanced Bonus Manager - Under Tests', 5, 1, 1221391649),
(66, 'Reveal Ip Location', 'iptocountry', 'Show Geo Location On Any Ip', 5, 1, 1221406559),
(67, 'Ban Spoof Emails', 'bannedemails', 'Ban Fake Email Address From Being Used On Sign Up', 5, 1, 1221937606),
(68, 'Mass Freeleech', 'massfree', 'Not All FreeDownload - Sets Individual Torrent''s To  Countstats On Or Off', 4, 1, 1222420548),
(69, 'Proxy Detect', 'proxy', 'Possible Proxy Users May Be Listed Here', 7, 1, 1222431165),
(70, 'Site Offline Control', 'siteonoff', 'Turn Site Offline - Staff access Only', 7, 1, 1223213931),
(71, 'Staff Actions Log', 'sysoplog', 'Staff Functions Log', 6, 1, 1224993991),
(72, 'Passkey Checker', 'selpasskey', 'View Users With More Than One Passkey', 5, 1, 1228089465);

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

INSERT INTO `stylesheets` (`id`, `uri`, `name`) VALUES
(1, 'default', '(default)'),
(2, 'large', 'Large text'),
(3, 'Klima', 'Klima'),
(4, 'Ei', 'Ei'),
(5, 'S-Graw', 'S-Graw');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `topicid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `topics`
--

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varchar(20) collate utf8_bin NOT NULL default '',
  `name` varchar(255) collate utf8_bin NOT NULL default '',
  `filename` varchar(255) collate utf8_bin NOT NULL default '',
  `save_as` varchar(255) collate utf8_bin NOT NULL default '',
  `search_text` text collate utf8_bin NOT NULL,
  `descr` text collate utf8_bin NOT NULL,
  `ori_descr` text collate utf8_bin NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') collate utf8_bin NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `banned` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `nfo` text collate utf8_bin NOT NULL,
  `points` int(10) NOT NULL default '0',
  `thanks` int(10) NOT NULL default '0',
  `anonymous` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `countstats` enum('yes','no') collate utf8_bin NOT NULL default 'yes',
  `multiplicator` int(10) unsigned NOT NULL default '0',
  `sticky` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `scene` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `request` enum('yes','no') collate utf8_bin NOT NULL default 'no',
  `poster` varchar(255) collate utf8_bin NOT NULL default 'poster.jpg',
  `url` varchar(150) collate utf8_bin default NULL,
  `nuked` enum('yes','no','unnuked') collate utf8_bin NOT NULL default 'no',
  `nukereason` varchar(100) collate utf8_bin NOT NULL default '',
  `tube` varchar(80) collate utf8_bin NOT NULL default '',
  `newgenre` varchar(100) collate utf8_bin NOT NULL default '',
  `afterpre` text collate utf8_bin,
  `uclass` int(10) unsigned NOT NULL,
  `checked_by` varchar(40) collate utf8_bin NOT NULL default '',
  `vip` enum('yes','no') collate utf8_bin default 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(30) collate utf8_bin NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(100) collate utf8_bin NOT NULL default '',
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
  KEY `username_2` (`last_access`,`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--