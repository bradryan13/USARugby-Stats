<?php
include_once './include_mini.php';

if (!isset($game_id) || !$game_id) {$game_id=$_GET['gid'];}
if (!isset($team_id) || !$team_id) {$team_id=$_GET['tid'];}

//get our team id and list of current rostered players
$roster = $db->getRoster($game_id, $team_id);
$roster_id=$roster['id'];
$comp_id=$roster['comp_id'];
$competition = $db->getCompetition($comp_id);
$player_ids = $roster['player_ids'];
$numbers = $roster['numbers'];
$frontrows = $roster['frontrows'];
$positions = $roster['positions'];


//get the team's UUID to match against players Team FSI ID
$query = "SELECT uuid, group_above_uuid FROM `teams` WHERE id = $team_id";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $uuid=$row['uuid'];
    $above_uuid=$row['group_above_uuid'];
}

//Get the players that are on this competition's event roster
$players = array();

// If bypass checkin was selected for the competition, then display players
// directly from teams.
if ($competition['bypass_checkin']) {
    $query = "SELECT id FROM `players` WHERE team_uuid IN ('$uuid', '$above_uuid') ORDER BY lastname ASC";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        $players[]=$row['id'];
    }
}
else {
    $query = "SELECT player_ids FROM `event_rosters` WHERE comp_id = $comp_id AND team_id=$team_id";
    $result = mysql_query($query);
    if (!empty($result)) {
        while ($row=mysql_fetch_assoc($result)) {
            $temp=$row['player_ids'];
        }
        $players = array_filter(explode('-', $temp));
    }
}

//Get the roster limit and comp type (15s or 7s) from the comp info
$query = "SELECT max_game, type FROM `comps` WHERE id = $comp_id";
$result = mysql_query($query);
if ($result) {
    while ($row=mysql_fetch_assoc($result)) {
        $max_game = $row['max_game'];
        $comp_type = $row['type'];
    }
}

//if this is not the first game, show button to fill with previous game's roster
$query = "SELECT * FROM `games` WHERE comp_id = $comp_id AND
(home_id = $team_id OR away_id = $team_id) ORDER BY kickoff LIMIT 1";
$result = mysql_query($query);
if ($result) {
    while ($row=mysql_fetch_assoc($result)) {
        if ($row['id'] != $game_id) {
            echo "<form name='pregame' id='pregame' />\n\r";
            echo "<input type='hidden' id='game_id' value='$game_id' />";
            echo "<input type='hidden' id='comp_id' value='$comp_id' />";
            echo "<input type='hidden' id='team_id' value='$team_id' />";
            echo "<input type='hidden' id='roster_id' value='$roster_id' />";
            echo "<input type='button' id='presubmit' class='btn btn-primary' value='Fill with Previous Roster' />";
            echo "</form>\n";
        }
    }
}

$options = array();

echo "<form name='grform' id='grform' />\n";

//Use our player ID's to get their names: lastname, firstname
foreach ($players as $player) {
    $options[$player]=rplayerName($player);
}

//Get options into alphebetical order
asort($options);


//Put numbers into an array
$numvals = explode('-', $numbers);

//make sure we can filter but allow zeros to pass by making them z temporarily
foreach ($numvals as &$num) {
    if ($num=='0') {
        $num='z';
    }
}

//filter our FALSE values
$numvals = array_filter($numvals);

foreach ($numvals as &$num) {
    if ($num=='z') {
        $num='0';
    }
    if ($num=='B') {
        $num='';
    }
}

//Put frontrows into an array
$frows = explode('-', $frontrows);

//make sure we can filter but allow zeros to pass by making them z temporarily
foreach ($frows as &$fr) {
    if ($fr=='0') {
        $fr='z';
    }
}

//filter our FALSE values
$frows = array_filter($frows);

foreach ($frows as &$fr) {
    if ($fr=='z') {
        $fr='0';
    }
}

//cplayers is an array to hold current player ids which is string seperated by -
$cplayers = array();
$cplayers = explode('-', substr($player_ids, 1, (strlen($player_ids)-2)));

$cpositions = array();
$cpositions = explode('-', substr($positions, 1, (strlen($positions)-2)));

//header for columns
echo "<table class='table'>\n";
$frhead = (!empty($comp_type) && ($comp_type == 1)) ? '<th>FR</th>' : '';
echo "<tr><th>Num.</th><th>Name</th><th>Position</th>$frhead</tr>\n";

//Create select for each roster spot and provide an option for each player for team
$max_game = empty($max_game) ? 0 : $max_game;
for ($j=1;$j<=$max_game;$j++) {
    echo "<tr>";

    //show number select, 0-99
    echo "<td><select id='n$j' class='input-small chzn-select'>\n";
    for ($k=0;$k<100;$k++) {
        if ($k==$numvals[$j]) {$stest = 'selected';} else {$stest='';}
        echo "<option id='n$j' $stest>$k</option>\n";
    }
    echo "</select></td>\n";

    //show player select
    echo "<td><select id='p$j' data-placeholder='Select Player' class='input-large chzn-select'>\n";
    echo "<option value=''></option>\n";

    $i=1;
    foreach ($options as $option => $name) {
        if (isset($cplayers[$j-1]) && $option==$cplayers[$j-1]) {$s='selected';} else {$s='';}
        echo "<option value=\"$option\" $s>$name</option>\n";
        $i++;
    }

    echo "</select></td>\n";
    echo "<td> <select data-placeholder='Select Position' class='input-large chzn-select' id='pos$j'>";

    $positions = getPositionList('selection');
    $current_player_position = empty($cpositions[$j - 1]) ? 'NIL' : $cpositions[$j - 1];
    foreach ($positions as $pos_code => $pos_name) {
        $selected = $current_player_position == $pos_code ? 'selected' : '';
        echo "<option value='$pos_code' $selected>$pos_name</option>\n";
    }
    echo "</select></td>";
    //show front row checkbox if this is a 15s comp
    if ($comp_type==1) {
        if ($frows[$j]==1) {$c="checked='checked'";} else {$c='';}
        echo "<td><input type='checkbox' id='fr$j' $c/></td>\n";
    }
    echo "</tr>\n";
}

echo "</table>\n";

//include hidden info necessary to run process page
echo "<input type='hidden' id='max' value='$max_game' />";
echo "<input type='hidden' id='game_id' value='$game_id' />";
echo "<input type='hidden' id='team_id' value='$team_id' />";
echo "<input type='hidden' id='roster_id' value='$roster_id' />";
echo "<input type='button' id='grsubmit' class='btn btn-primary' value='Update Roster' />";
echo "</form>\n";
