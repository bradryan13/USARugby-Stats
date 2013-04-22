<?php
/*
 * Resque job to get all the teams players.
 */

include_once __DIR__ . '/../APSource.php';
include_once __DIR__ . '/../DataSource.php';
include_once __DIR__ . '/../../../vendor/autoload.php';
use Source\APSource;
use Source\DataSource;

class CreateGame {
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
		$event = $this->args['event'];
        $game_id = $this->args['game_id'];
        $teams_by_resource = $this->args['teams_by_resource'];
        $resource_by_team = $this->args['resource_by_team'];
		$user = $db->getUser($config['admin_user_uuid']);
		$oauth_config = array(
			'consumer_key' => $config['consumer_key'],
			'consumer_secret' => $config['consumer_secret'],
			'auth_token' => $user['token'],
			'auth_secret' => $user['secret']
		);
		$client = APSource::SourceFactory($oauth_config, $config['auth_domain']);

        $event = $client->createEvent($event);

        $db->updateGame($game_id, array('uuid' => $event->uuid));

        // Add Resources From Synced Data.
        // Safety check.
        if (!empty($event->resource_ids)) {
            $event_resource = reset($event->resource_ids);
            if ($event_resource->uuid == $selected_resource['uuid']) {
                $location = (array) $event_resource->location;
                $teams_by_resource[$event_resource->uuid]['location'] = $location;
            }
        }

        if (!empty($teams_by_resource)) {
            foreach ($teams_by_resource as $res_uuid => $resource_data) {
                if ($existing_resource = $db->getResource($resource_data['uuid'])) {
                    $db->updateResource($existing_resource['id'], $resource_data);
                } else {
                    $db->addResource($resource_data);
                }
            }
        }

        if (!empty($resource_by_team)) {
            foreach ($resources_by_team as $team_uuid => $synced_team_resources) {
                $team = $db->getTeam($team_uuid);
                $team_resources = $team['resources'];
                foreach ($synced_team_resources as $resource) {
                    $team_resources[] = $resource['uuid'];
                }
                $team['resources'] = array_unique($team_resources);
                $db->updateTeam($team['id'], $team);
            }
        }
    }

}