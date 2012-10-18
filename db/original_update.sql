ALTER TABLE users ADD uuid char(36);
ALTER TABLE users ADD UNIQUE (uuid);
ALTER TABLE users ADD token char(40);
ALTER TABLE users ADD secret char(40);
ALTER TABLE games ADD uuid char(36);
ALTER TABLE games ADD UNIQUE (uuid);
ALTER TABLE users DROP password;
ALTER TABLE players CHANGE team_fsi_id team_uuid char(36);
ALTER TABLE players CHANGE fsi_id uuid char(36);
ALTER TABLE teams CHANGE fsi_id uuid char(36);
ALTER TABLE teams ADD UNIQUE (uuid);
ALTER TABLE users CHANGE login login VARCHAR(64)  NOT NULL  DEFAULT '';
ALTER TABLE comps CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE event_rosters CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE game_events CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE game_rosters CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE games CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE players CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE teams CHANGE user_create user_create varchar(64) NOT NULL;
ALTER TABLE game_rosters ADD positions TEXT  NULL  AFTER frontrows;
ALTER TABLE `teams` ADD `resources` LONGBLOB  NULL  AFTER `short`;
ALTER TABLE `games` CHANGE `field_num` `field_num` CHAR(36)  NULL  DEFAULT '';
CREATE TABLE `resources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL DEFAULT '',
  `title` varchar(120) DEFAULT NULL,
  `location` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

