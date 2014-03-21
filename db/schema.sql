CREATE TABLE `comp_top_group` (
  `id` int(11) unsigned NOT NULL,
  `team_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `name` varchar(80) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` int(1) NOT NULL,
  `max_event` int(2) NOT NULL,
  `max_game` int(2) NOT NULL,
  `hidden` int(1) NOT NULL,
  `league_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ct_pairs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp_id` int(6) NOT NULL,
  `team_id` int(6) NOT NULL,
  `division_id` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comp_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_rosters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `last_edit` datetime NOT NULL,
  `comp_id` int(5) NOT NULL,
  `team_id` int(6) NOT NULL,
  `player_ids` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(3) NOT NULL,
  `name` varchar(30) NOT NULL,
  `value` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `game_id` int(7) NOT NULL,
  `team_id` int(6) NOT NULL,
  `player_id` int(7) NOT NULL,
  `type` int(2) NOT NULL,
  `minute` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_rosters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `last_edit` datetime NOT NULL,
  `comp_id` int(5) NOT NULL,
  `game_id` int(6) NOT NULL,
  `team_id` int(6) NOT NULL,
  `player_ids` text NOT NULL,
  `numbers` text NOT NULL,
  `frontrows` text NOT NULL,
  `positions` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `comp_id` int(5) NOT NULL,
  `comp_game_id` int(3) NOT NULL,
  `home_id` int(7) NOT NULL,
  `away_id` int(7) NOT NULL,
  `kickoff` datetime NOT NULL,
  `field_num` char(36) DEFAULT '',
  `home_score` int(3) NOT NULL,
  `away_score` int(3) NOT NULL,
  `ref_id` int(6) NOT NULL,
  `ref_sign` int(1) NOT NULL,
  `4_sign` int(1) NOT NULL,
  `home_sign` int(1) NOT NULL,
  `away_sign` int(1) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `field_loc` char(50) DEFAULT '',
  `field_addr` varchar(200) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_create` varchar(64) NOT NULL,
  `last_update` datetime NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `team_uuid` char(36) DEFAULT NULL,
  `firstname` varchar(25) NOT NULL,
  `lastname` varchar(35) NOT NULL,
  `picture_url` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eta` int(11) NOT NULL,
  `item` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL DEFAULT '',
  `title` varchar(120) DEFAULT NULL,
  `location` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hidden` int(1) NOT NULL,
  `user_create` varchar(64) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `short` varchar(30) NOT NULL,
  `resources` longblob,
  `logo_url` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `group_above_uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) DEFAULT NULL,
  `team` int(6) NOT NULL,
  `access` int(2) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `token` char(40) DEFAULT NULL,
  `secret` char(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
