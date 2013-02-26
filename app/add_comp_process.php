<?php
include_once './include_mini.php';

$id = mysql_real_escape_string($request->get('id'));
$name = ereg_replace("[^A-Za-z0-9 ]", "", trim($request->get('name')));
$name = mysql_real_escape_string($name);
$type = mysql_real_escape_string($request->get('type'));
$league_type = mysql_real_escape_string($request->get('league_type'));
$max_event = mysql_real_escape_string($request->get('max_event'));
$max_match = mysql_real_escape_string($request->get('max_match'));
$start_date = mysql_real_escape_string($request->get('start_date'));
$end_date = mysql_real_escape_string($request->get('end_date'));
$top_groups = $request->get('top_groups');
$bypass_checkin = $request->get('bypass_checkin');
$bypass_checkin = !empty($bypass_checkin) ? $bypass_checkin == 'on' : 0;
$result = $db->addupdateCompetition(array(
    'id' => $id,
    'user_create' => $_SESSION['user'],
    'name' => $name,
    'start_date' => $start_date,
    'end_date' => $end_date,
    'type' => $type,
    'league_type' => $league_type,
    'max_event' => $max_event,
    'max_game' => $max_match,
    'hidden' => '0',
    'top_groups' => $top_groups,
    'bypass_checkin' => $bypass_checkin
));

header("Location: http://" . $_SERVER['HTTP_HOST']);
