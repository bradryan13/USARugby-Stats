<?php

use Source\APSource;
use Source\DataSource;
/**
 * used to get a players team in a game for adding a scoring event
 * we look for the player id in the players list
 * must use !== in case its at the 0 spot
 *
 * @param unknown $player_id
 * @param unknown $game_id
 * @return unknown
 */

function getTeam($player_id, $game_id)
{
    $query = "SELECT * FROM `game_rosters` WHERE game_id = '$game_id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {

        if (strpos($row['player_ids'], "-$player_id-")!==false)
            return $row['team_id'];

    }
}

/**
 * get event value to update score
 *
 * @param unknown $type
 * @return unknown
 */
function getValue($type)
{
    $query = "SELECT value FROM `event_types` WHERE event_id = '$type'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        return $row['value'];

    }
}

/**
 * when we add a score we need to update the game score
 *
 * @param unknown $game_id
 */
function updateScore($game_id)
{
    //first get the id's of the home and away teams
    $query = "SELECT * FROM `games` WHERE id='$game_id'";
    $result = mysql_query($query);
    $homep = 0;
    $awayp = 0;
    while ($row=mysql_fetch_assoc($result)) {
        $home_id = $row['home_id'];
        $away_id = $row['away_id'];
        $game_uuid = $row['uuid'];
    }

    //get total home team points using getValue function
    $query = "SELECT * FROM `game_events` WHERE game_id = '$game_id' AND type<10 AND team_id='$home_id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        $homep = $homep+getValue($row['type']);
    }

    //get total away team points using getValue function
    $query = "SELECT * FROM `game_events` WHERE game_id = '$game_id' AND type<10 AND team_id='$away_id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        $awayp = $awayp+getValue($row['type']);
    }

    //update score
    $query = "UPDATE `games` SET home_score='$homep', away_score='$awayp' WHERE id = '$game_id'";
    $result = mysql_query($query);
    include_once './config.php';
    Resque::setBackend('redis://redis:' . $config['redis_password'] . '@' . $config['redis_host']);

    $db = new DataSource;
    $home_team = $db->getTeam($home_id);
    $away_team = $db->getTeam($away_id);
    $competitors = array(
        0 => array(
            'uuid' => $home_team['uuid'],
            'label' => 'home',
            'score' => $homep
        ),
        1 => array(
            'uuid' => $away_team['uuid'],
            'label' => 'away',
            'score' => $awayp
        )
    );
    Resque::enqueue('update_game_score', 'UpdateGameScore', array('game_uuid' => $game_uuid, 'competitors' => $competitors));
}


/**
 *
 *
 * @param unknown $id
 * @return unknown
 */
function pExists($id)
{
    $query = "SELECT id FROM `players` WHERE uuid = '$id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        if ($row['id']) {return true;} else {return false;}
    }
}


/**
 *
 *
 * @param unknown $id
 * @return unknown
 */
function tExists($id)
{
    $query = "SELECT id FROM `teams` WHERE uuid = '$id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        if ($row['id']) {return true;} else {return false;}
    }
}

function getPositionList($op = 'selection')
{
    if ($op == 'selection') {
        $positions = array(
            'NIL' => '',
            'LHP' => 'Loose-Head Prop',
            'H' => 'Hooker',
            'THP' => 'Tight-Head Prop',
            'L1' => 'Lock 1',
            'L2' => 'Lock 2',
            'OSF' => 'Open Side Flanker',
            'BSF' => 'Blind Side Flanker',
            'N8' => 'Number 8',
            'SH' => 'Scrum Half',
            'FH' => 'Fly Half',
            'IC' => 'Inside Center',
            'OC' => 'Outside Center',
            'W1' => 'Wing 1',
            'W2' => 'Wing 2',
            'FB' => 'Fullback',
        );
        // Add 8 Reserves.
        for ($i = 1; $i <= 8; $i++) {
          $positions['R' . $i] = 'Reserve ' . $i;
        }
        $display_positions = getPositionList('display');
        foreach ($positions as $pos_code => $pos_name) {
            if (!empty($pos_name)) {
                $positions[$pos_code] = $pos_name . ' (' . $display_positions[$pos_code] . ')';
            }
        }
    }
    else {
        $positions = array(
          'NIL' => '',
          'LHP' => 'P',
          'H' => 'H',
          'THP' => 'P',
          'L1' => 'L',
          'L2' => 'L',
          'OSF' => 'F',
          'BSF' => 'F',
          'N8' => 'N8',
          'SH' => 'SH',
          'FH' => 'FH',
          'IC' => 'C',
          'OC' => 'C',
          'W1' => 'W',
          'W2' => 'W',
          'FB' => 'FB',
        );
        // Add 8 Reserves.
        for ($i = 1; $i <= 8; $i++) {
          $positions['R' . $i] = 'R';
        }
    }

    return $positions;
}

function getResourceMapUrl($resource = NULL, $resource_id = NULL) {
    if (empty($resource)) {
        $db = new Source\DataSource;
        $resource = $db->getResource($resource_id);
    }
    if (!$resource_loc = $resource['location']) {
        return FALSE;
    }
    $loc_url = 'http://maps.google.com/?q=';
    $usable_loc_fiels = array('street', 'city', 'state', 'zip', 'country');
    $count = 1;
    $max_count = count($usable_loc_fiels);
    foreach ($usable_loc_fiels as $loc_field) {
        if (!empty($resource_loc[$loc_field])) {
            $loc_url .= $resource_loc[$loc_field];
        }
        if ($count < $max_count && !empty($resource_loc[$loc_field])) {
            $loc_url .= '+';
        }
        $count ++;
    }
    return $loc_url;
}
