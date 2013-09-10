<?php
include_once './include_mini.php';
include './config.php';

$base_url = $request->getScheme() . '://' . $request->getHost();

if (!isset($comp_id) || !$comp_id) {$comp_id=$_GET['id'];}

$query = "SELECT g.*, c.league_type FROM games g JOIN comps c ON g.comp_id=c.id WHERE comp_id = $comp_id ORDER BY kickoff";
$result = mysql_query($query);
$game_rows = array();
while ($row=mysql_fetch_assoc($result)) {
    // Get infomation for each game
    $game = array();
    $game['canedit'] = false;
    $game['kickoff'] = date('n/j g:ia', strtotime($row['kickoff']));
    $resource = $db->getResource($row['field_num']);
    $game['league'] = $row['league_type'];
    if (!empty($row['field_loc']) && !empty($row['field_addr'])) {
        $game['location'] = "<a target='_blank' href ='https://maps.google.com/?q=" . $row['field_addr'] . "'>" . $row['field_loc'] . "<span class='icon-map-marker'></span></a></li>";
    }

    if (editCheck(1)) {
        $game['canedit'] = true;
        $game['id'] = $row['id'];
        $game['comp_id'] = $comp_id;
        // Modals:
        if (empty($twig)) {
            $loader = new Twig_Loader_Filesystem(__DIR__.'/views');
            $twig = new Twig_Environment($loader, array());
        }

        $rosteriframe = array(
            'entity' => 'roster',
            'eid' => $row['id'],
            'title' => 'Roster', // TODO: What is this roster's name?
            'iframe_url' => "$base_url/game.php?iframe=1&id={$row['id']}&ops[0]=game_rosters",
        );
        $game['rosteriframe'] = $twig->render('modal-template-iframe.twig', array('modal' => $rosteriframe));

        $gameiframe = array(
            'entity' => 'game',
            'eid' => $row['id'],
            'title' => 'Game info',
            'iframe_url' => "$base_url/game.php?iframe=1&id={$row['id']}&ops[0]=game_info&ops[1]=game_score&ops[2]=game_rosters&ops[3]=game_score_events&ops[4]=game_sub_events&ops[5]=game_card_events",
        );
        $game['gameiframe'] = $twig->render('modal-template-iframe.twig', array('modal' => $gameiframe));

        //Home Team Schedule
        $homeschedule = array(
            'entity' => 'homeschedule',
            'eid' => $row['id'],
            'title' => 'Home Schedule',
            'iframe_url' => "$base_url/team_games.php?iframe=1&team_id={$row['home_id']}",
        );
        $game['homeschedule'] = $twig->render('modal-template-iframe.twig', array('modal' => $homeschedule));

        //Away Team Schedule
        $awayschedule = array(
            'entity' => 'awayschedule',
            'eid' => $row['id'],
            'title' => 'Away Schedule',
            'iframe_url' => "$base_url/team_games.php?iframe=1&team_id={$row['away_id']}",
        );
        $game['awayschedule'] = $twig->render('modal-template-iframe.twig', array('modal' => $awayschedule));

        //Event on AllPlayers.com
        $linktoapgame = array(
            'entity' => 'linktoapgame',
            'eid' => $row['id'],
            'title' => 'Event on AllPlayers.com',
            'iframe_url' => "{$config['auth_domain']}/groups/uuid/{$row['uuid']}",
        );
        $game['linktoapgame'] = $twig->render('modal-template-iframe.twig', array('modal' => $linktoapgame));
    }

    $game['comp_game_id'] = $row['comp_game_id'];
    $game['home_id'] = teamName($row['home_id']);
    $game['score'] = "<a href='game.php?id={$row['id']}'>{$row['home_score']} - {$row['away_score']}</a>";
    $game['away_id'] = teamName($row['away_id']);
    $game['field'] = $resource['title'];
    $game_rows[] = $game;
}

if (empty($twig)) {
    $loader = new Twig_Loader_Filesystem(__DIR__.'/views');
    $twig = new Twig_Environment($loader, array());
}

echo $twig->render('comp-games.twig', array('gamerows' => $game_rows));

