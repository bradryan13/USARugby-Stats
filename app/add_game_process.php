<?php
include_once './include_mini.php';
use Source\APSource;

$game_num = $request->get('gnum');
$kod = $request->get('kdate');
$koh = $request->get('koh');
$kom = $request->get('kom');
$home = $request->get('home');
$away = $request->get('away');
$comp_id = $request->get('comp_id');
$resources_by_team = $request->get('resources_by_team');
$teams_by_resource = $request->get('teams_by_resource');
$selected_resource = $request->get('selected_resource');
$field_location = $request->get('field_loc');
$field_address = $request->get('field_addr');



if ($oauth_disabled) {
  $client = APSource::BasicAuthFactory($_SESSION['user'], $_SESSION['password']);
}
else {
  $client = APSource::SessionSourceFactory();
}

$home_team = $db->getTeam($home);
$away_team = $db->getTeam($away);
$userTimezone = new DateTimeZone((isset($config['timezone']) ? $config['timezone'] : 'America/Chicago'));
$date_time = new DateTime($kod . 'T' . $koh . ':' . $kom, $userTimezone);
$date_time_ap = clone $date_time;
$date_time_ap->setTimezone(new DateTimeZone('UTC'));
$date_time_ap = $date_time_ap->format('Y-m-d\TH:i:s');


$kfull = $date_time->format('Y-m-d H:i:\0\0');

$game_info = array(
    'id' => '',
    'user_create' => $_SESSION['user'],
    'comp_id' => $comp_id,
    'comp_game_id' => $game_num,
    'home_id' => $home_team['id'],
    'away_id' => $away_team['id'],
    'kickoff' => $kfull,
    'field_num' => empty($selected_resource['uuid']) ? NULL : $selected_resource['uuid'],
    'home_score' => '0',
    'away_score' => '0',
    'ref_id' => '0',
    'ref_sign' => '0',
    '4_sign' => '0',
    'home_sign' => '0',
    'away_sign' => '0',
    'field_loc' => $field_location,
    'field_addr' => $field_address,
    'uuid' => NULL,
);

$game = $db->addGame($game_info);
$game_id = mysql_insert_id();

$event = array(
    'title' => $away_team['name'] . ' @ ' . $home_team['name'],
    'description' => $away_team['name'] . ' @ ' . $home_team['name'],
    'date_time' => array(
        'start' => $date_time_ap,
        'end' => $date_time_ap
    ),
    'competitors' => array(
        0 => array(
            'uuid' => $home_team['uuid'],
            'label' => 'home'
        ),
        1 => array(
            'uuid' => $away_team['uuid'],
            'label' => 'away'
        )
    ),
    'category' => 'game',
    'external_id' => 'STATS_APP_GAME_' . $game_id,
);
if (!empty($selected_resource)) {
    $event['groups'] = array($selected_resource['teamOwner']);
    $event['resources'] = array($selected_resource['uuid']);
}
else {
    $event['groups'] = array($home_team['uuid']);
}

include './config.php';
Resque::enqueue('create_game', 'CreateGame', array('event' => $event, 'game_id' => $game_id, 'teams_by_resource' => $teams_by_resource, 'resource_by_team' => $resources_by_team));
$now = date('Y-m-d H:i:s');
$numbers = '-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-';
$frontrows = '-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-0-';
$roster_data = array(
    'id' => '',
    'user_create' => $_SESSION['user'],
    'last_edit' => $now,
    'comp_id' => $comp_id,
    'game_id' => $game_id,
    'team_id' => $home_team['id'],
    'player_ids' => NULL,
    'numbers' => $numbers,
    'frontrows' => $frontrows,
    'positions' => NULL,
);

// Add home.
$db->addRoster($roster_data);

// Add away.
$roster_data['team_id'] = $away_team['id'];
$db->addRoster($roster_data);

