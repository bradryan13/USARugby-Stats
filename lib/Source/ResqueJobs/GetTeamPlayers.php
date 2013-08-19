<?php
/* 
 * Resque job to get all the teams players.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class GetTeamPlayers {
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
		$now = date('Y-m-d H:i:s');
		$team_uuid = $this->args['team_uuid'];
		$existing_players = $db->getTeamPlayers($team_uuid);
		$user = $db->getUser($config['admin_user_uuid']);
		$oauth_config = array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
			'auth_token' => $user['token'],
			'auth_secret' => $user['secret']
		);
		$client = APSource::SourceFactory($oauth_config, $config['auth_domain']);
		$members = $client->getGroupMembers($team_uuid);
		$players = array();
		foreach ($members as $member) {
			if (!empty($member->picture)) {
				$picture_url = substr($member->picture, strpos($member->picture, '/sites/default/'));
			}
			else {
				$picture_url = '/sites/default/files/imagecache/profile_mini/sites/all/themes/allplayers960/images/default_profile.png';
			}
			$player_info = array(
					'user_create' => $user['login'],
					'last_update' => $now,
					'uuid' => $member->uuid,
					'team_uuid' => $team_uuid,
					'firstname' => mysql_real_escape_string($member->fname),
					'lastname' => mysql_real_escape_string($member->lname),
					'picture_url' => $picture_url,
					'roles' => ($existing_players && $existing_players[$member->uuid] && $existing_players[$member->uuid]['roles']) ? $existing_players[$member->uuid]['roles'] : NULL
			);
			if ($existing_players && key_exists($member->uuid, $existing_players) && !empty($existing_players[$member->uuid]['id'])) {
				$player_info['id'] = $existing_players[$member->uuid]['id'];
			}
			$players[$member->uuid] = $member->uuid;
			$db->addupdatePlayer($player_info);
		}
		if ($existing_players) {
			$removed_players = array_diff_key($existing_players, $players);
			foreach ($removed_players as $player) {
				$db->removePlayer($player['id']);
			}
		}
	}
}

?>
