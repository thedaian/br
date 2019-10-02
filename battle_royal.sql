-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 30, 2006 at 05:56 PM
-- Server version: 4.1.8
-- PHP Version: 5.0.3
-- 
-- Database: `battle_royal`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `characters`
-- 

CREATE TABLE `characters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `game_id` int(10) unsigned NOT NULL default '0',
  `game_applied` int(10) unsigned NOT NULL default '0',
  `name` varchar(40) NOT NULL default '',
  `description` text NOT NULL,
  `gender` tinyint(1) unsigned NOT NULL default '0',
  `health` smallint(6) NOT NULL default '0',
  `max_health` smallint(6) NOT NULL default '0',
  `pos_x` tinyint(3) unsigned NOT NULL default '0',
  `pos_y` tinyint(3) unsigned NOT NULL default '0',
  `next_x` tinyint(3) unsigned NOT NULL default '0',
  `next_y` tinyint(3) unsigned NOT NULL default '0',
  `weapon_id` int(10) unsigned NOT NULL default '0',
  `weapon_ammo` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `game_id` (`game_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `poster_id` int(10) unsigned NOT NULL default '0',
  `time_posted` int(10) unsigned NOT NULL default '0',
  `options` tinyint(3) unsigned NOT NULL default '0',
  `view_level` tinyint(3) unsigned NOT NULL default '0',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `poster_id` (`poster_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `games`
-- 

CREATE TABLE `games` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `owner_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(20) NOT NULL default '',
  `description` text NOT NULL,
  `map_type` varchar(5) NOT NULL default '',
  `males` tinyint(3) unsigned NOT NULL default '0',
  `females` tinyint(3) unsigned NOT NULL default '0',
  `max_players` tinyint(3) unsigned NOT NULL default '0',
  `current_males` tinyint(3) unsigned NOT NULL default '0',
  `current_females` tinyint(3) unsigned NOT NULL default '0',
  `applied` tinyint(3) unsigned NOT NULL default '0',
  `creation_time` int(10) unsigned NOT NULL default '0',
  `start_time` int(10) unsigned NOT NULL default '0',
  `end_time` int(10) unsigned NOT NULL default '0',
  `last_activity` int(10) unsigned NOT NULL default '0',
  `options` tinyint(3) unsigned NOT NULL default '0',
  `start_health` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `maps`
-- 

CREATE TABLE `maps` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `game_id` int(10) unsigned NOT NULL default '0',
  `pos_x` tinyint(3) unsigned NOT NULL default '0',
  `pos_y` tinyint(3) unsigned NOT NULL default '0',
  `players` tinyint(3) unsigned NOT NULL default '0',
  `options` tinyint(3) unsigned NOT NULL default '0',
  `notes` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `game_id` (`game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `messages`
-- 

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender_id` int(10) unsigned NOT NULL default '0',
  `receiver_id` int(10) unsigned NOT NULL default '0',
  `game_id` int(10) unsigned NOT NULL default '0',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `time_sent` int(10) unsigned NOT NULL default '0',
  `msg_text` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `game_id` (`game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `sessions`
-- 

CREATE TABLE `sessions` (
  `sessionID` varchar(50) NOT NULL default '',
  `user_id` int(10) unsigned NOT NULL default '0',
  `session_begun` int(10) unsigned NOT NULL default '0',
  `end` int(10) unsigned NOT NULL default '0',
  `action` varchar(30) NOT NULL default '',
  `user_IP` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sessionID`),
  KEY `user_id` (`user_id`),
  KEY `user_IP` (`user_IP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `settings`
-- 

CREATE TABLE `settings` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `options` tinyint(3) unsigned NOT NULL default '0',
  `max_games` tinyint(3) unsigned NOT NULL default '0',
  `max_chars` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(30) NOT NULL default '',
  `password` text NOT NULL,
  `salt` varchar(12) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `last_login` int(10) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  `user_level` tinyint(3) unsigned NOT NULL default '0',
  `last_IP` int(10) unsigned NOT NULL default '0',
  `gamesIN` int(10) unsigned NOT NULL default '0',
  `gamesRUN` int(10) unsigned NOT NULL default '0',
  `gamesTOTAL` int(10) unsigned NOT NULL default '0',
  `gamesSURVIVED` int(10) unsigned NOT NULL default '0',
  `gamesDIED` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `last_IP` (`last_IP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `weapons`
-- 

CREATE TABLE `weapons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `description` text NOT NULL,
  `ammo` smallint(6) NOT NULL default '0',
  `min_dmg` smallint(5) unsigned NOT NULL default '0',
  `max_dmg` smallint(5) unsigned NOT NULL default '0',
  `creator_ID` int(10) unsigned NOT NULL default '0',
  `notes` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `creator_ID` (`creator_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
