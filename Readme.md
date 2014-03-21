## USA Rugby Statistics system

This system, originally developed by Matt Trenary, provides an interface and tracking for USA Rugbys National Championship Series.
"The National Championship Series web application is designed to facilitate the management of championship event rosters, scores, and game information. It is designed to work from any phone, computer, or tablet that can access the internet."


### Queue

Run an individual queue item

```sh
bin/console queue:run
```

### Database

To configure the database create file `app/config.php` with the following structure:

```PHP
<?php

$config = array(
  'username' => 'user',
  'password' => 'pass',
  'database' => 'db',
  'server'   => '127.0.0.1',
  'consumer_key'    => 'DEADBEEF',
  'consumer_secret' => 'BEEFDEAD',
  'auth_domain'     => 'https://www.allplayers.com', // Optional
  'admin_group_uuid' => 'ffc1b780-cc4c-11e1-9e39-12313d2a2278',
  'verify_peer' => TRUE // FALSE if you're connecting to sandbox where HTTPS is invalid.
  'cdn' => 'https://d2xe74i6zxd0fz.cloudfront.net'
);
```

Included under directory: "db" is the DDL used for the system; if there are updates to the structure this can be documented using the following command:


`mysqldump -d <db_name> --skip-add-drop-table --skip-add-locks --skip-disable-keys --skip-set-charset --skip-comments --compact -u<user> -p<pass> > db/schema.sql`

#### Migrations/Updates

Information about current updates and status of the database schema can be managed with the doctrine cli:

`bin/doctrine migrations:status`

Updating to the latest version:

`bin/doctrine migrations:migrate`

### Javascript and CSS.
In order to add in the proper javascript, in your commandline in the top level directory run `make`.  
If you choose to update the hash on any of the files, be sure to run make to update them.


### Composer

Note this project uses [composer](http://getcomposer.org/) to manage dependencies.

Quick start:

```
git clone ...
composer.phar install
```

### OAuth

Generate a token for this app: http://develop.allplayers.com/oauth.html

### Attribution

*  [Matt Trenary](https://github.com/matttrenary)
*  https://github.com/christianchristensen/AllPlayers-OAuth


### Iframe Usage

Currently games support iframeable elements.
There are several parameters you can pass in the [GET] request to `/game.php` in order to properly retrieve iframes.

*  `?iframe=TRUE`
	*  Must be passed for all iframe requests
*  `?id=`
	*  This can either be a uuid of a game or an id.
*  `?uuid=`
	*  Either the `id` or `uuid` may be included in the request, but the `id` take priority.
*  `?ops=`
	*  An array of parts of the page you want to receive in the iframe.
	*  Possible options:
		*  `game_info`
		*  `game_score`
		*  `game_rosters`
		*  `game_score_events`
		*  `game_sub_events`
		*  `game_card_events`


###### Examples
*  `https://usarugbystats.pdup.allplayers.com/game.php?id=123&iframe=TRUE&ops[0]=game_info`
*  `https://usarugbystats.pdup.allplayers.com/game.php?id=123&iframe=TRUE&ops[0]=game_info&ops[1]=game_rosters&ops[2]=game_sub_events`


### Standings

Additionally, you can also request standings for a league or division, in order to do that you have to specify a
group to display standings in when you are creating the competition.


###### Examples
*  `https://usarugbystats.pdup.allplayers.com/standings?iframe=TRUE&group_uuid=a74fedb0-d1ba-11e1-9e39-12313d2a2278`
    * Get the html representation of standings based on sportsml generated and interpreted with our xsl sheet.
*  `https://usarugbystats.pdup.allplayers.com/standings.xml?iframe=TRUE&group_uuid=a74fedb0-d1ba-11e1-9e39-12313d2a2278`
    * Get the xml file with all the standings data formatted in sportsml.

### Testing

[![Build Status](https://secure.travis-ci.org/AllPlayers/USARugby-Stats.png)](http://travis-ci.org/AllPlayers/USARugby-Stats)


#### Mink/Behat Testing

In order to start using the testing framework, you first have to install mink/behat from composer dev so run:
```
compose update --dev
```
Next step is to modify your behat.yml.dist file:
```
default:
  context:
    class:  'FeatureContext'
  extensions:
    Behat\MinkExtension\Extension:
      goutte:    ~
      selenium2: ~
```
And finally, run the tests by running:
```
bin/behat
```

Optionally, add params like host:
```
export BEHAT_PARAMS="formatter[name]=progress&context[parameters][base_url]=http://localhost"
```

To look at mink extensions available that you can use in your tests,
*   https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Context/MinkContext.php

To create your own,
*   https://github.com/AllPlayers/USARugby-Stats/blob/master/features/bootstrap/FeatureContext.php#L8

