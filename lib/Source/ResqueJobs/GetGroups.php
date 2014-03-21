<?php
/* 
 * Resque job to get all the admin teams.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class GetGroups {
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
    $existing_teams = $db->getAllTeams();
    $teams = array();
    $offset = 0;
		$user = $db->getUser($config['admin_user_uuid']);
		$oauth_config = array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
			'auth_token' => $user['token'],
			'auth_secret' => $user['secret']
		);
		$client = APSource::SourceFactory($oauth_config, $config['auth_domain']);
    do {
      $response = $client->userGetMyGroups($config['admin_user_uuid'], '*,group_type', $offset, 1000);
      $offset+= 1;
      $teams = array_merge($teams, (array)$response);
    } while (sizeof($response) == 1000);
    foreach ($teams as $team) {
      $team = (is_array($team)) ? $team : (array) $team;
      if (!empty($team['logo'])) {
        $logo_url = substr($team['logo'], strpos($team['logo'], '/sites/default/'));
      } else {
        $logo_url = '/sites/default/files/imagecache/img_120x120_s/sites/all/modules/apci_features/apci_defaults/group-icon.png';
      }   
      $team_info = array(
        'hidden' => 0,
        'user_create' => $user['login'],
        'uuid' => $team['uuid'],
        'name' => $team['title'],
        'short' => $team['title'],
        'logo_url' => $logo_url,
        'description' => $team['description'],
        'type' => $team['group_type'],
      );  
      if (!key_exists($team['uuid'], $existing_teams)) {
        $db->addupdateTeam($team_info);
        $added++;
        $existing_teams[$team['uuid']] = $team_info;
        #Resque::enqueue('get_group', 'GetGroup', array('team_uuid' => $team['uuid']));
      }   
      else {
        if (!empty($existing_teams[$team['uuid']]['id']) && is_numeric($existing_teams[$team['uuid']]['id'])) {
          $team_info['id'] = $existing_teams[$team['uuid']]['id'];
          $team_info['group_above_uuid'] = $existing_teams[$team['uuid']]['group_above_uuid'];
          $db->addupdateTeam($team_info);
          if (empty($team_info['group_above_uuid'])) {
            #Resque::enqueue('get_group', 'GetGroup', array('team_uuid' => $team['uuid']));
          }
        }
      }
    }
  }
}
