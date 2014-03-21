<?php
/* 
 * Resque job to get all the admin teams.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class GetGroupAbove {
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
    		$teams = $db->getAllTeams();
   		foreach ($teams as $team) {
      			if (!$team['group_above_uuid'] && !$team['deleted'] && $team['type'] == 'team') {
        			Resque::enqueue('get_group', 'GetGroup', array('team_uuid' => $team['uuid']));
      			}   
		}
    	}
}
