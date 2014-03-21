<?php
/* 
 * Resque job to get all the admin teams.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class GetGroup {
	/* Config settings. */
	public $config;

	public function __construct()
	{
		include __DIR__ . '/../../../app/config.php';
		$this->config = $config;
	}
	
	public function perform() {
		$db = new DataSource();
		$config = $this->config;
		$user = $db->getUser($config['admin_user_uuid']);
		$oauth_config = array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
			'auth_token' => $user['token'],
			'auth_secret' => $user['secret']
		);
		$client = APSource::SourceFactory($oauth_config, $config['auth_domain']);
		try {
		    	$group = $client->groupsGetGroup($this->args['team_uuid']);
		    	if (!empty($group->groups_above_uuid)) {
		      		$group_above = array_pop($group->groups_above_uuid);
		      		$db->updateTeamAbove($this->args['team_uuid'], $group_above);
		    	}  	
		} catch (Guzzle\Http\Exception\BadResponseException $e) {
			if ($e->getResponse()->getStatusCode() == 404) {
				$db->updateTeamDelete($this->args['team_uuid']);
			}
		}
	}
}
