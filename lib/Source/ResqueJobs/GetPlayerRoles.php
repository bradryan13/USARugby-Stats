<?php
/* 
 * Resque job to get all player roles.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
use Source\APSource;
use Source\DataSource;

class GetPlayerRoles {
	/* Config variable */
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
		$member_roles = $client->groupsGetRoles($this->args['team_uuid'], $this->args['player_uuid']);
		$roles = array();
		foreach ($member_roles as $role) {
			$roles[] = $role->name;
		}
		$roles = (empty($roles)) ? NULL : serialize($roles);
		$now = date('Y-m-d H:i:s');
		$player_info = array(
			'last_update' => $now,
			'roles' => $roles
		);
		$player_info = array_merge($db->getPlayer($this->args['player_uuid'], $this->args['team_uuid']), $player_info);
		$db->addupdatePlayer($player_info);
	}
}

?>
