<?php
/*
 * Resque job to get all the teams players.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class UpdateGameScore {
    /* Config settings. */
	public $config;

	public function __construct()
	{
		include __DIR__ . '/../../../app/config.php';
		Resque::setBackend('redis://redis:' . $config['redis_password'] . '@' . $config['redis_host']);
		$this->config = $config;
	}

    public function perform() {
		$db = new DataSource();
		$config = $this->config;
		$game_uuid = $this->args['game_uuid'];
		$competitors = $this->args['competitors'];
		$user = $db->getUser($config['admin_user_uuid']);
		$oauth_config = array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
			'auth_token' => $user['token'],
			'auth_secret' => $user['secret']
		);
		$client = APSource::SourceFactory($oauth_config, $config['auth_domain']);
        $client->updateEvent($game_uuid, array('competitors' => $competitors));
    }

}