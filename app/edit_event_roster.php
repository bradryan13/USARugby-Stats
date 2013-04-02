<?php
include_once './include_mini.php';

if (!isset($roster_id) || !$roster_id) {$roster_id=$_GET['id'];}

// @TODO: Move queries to use DataSource or remove completely.

//get our team id and list of current rostered players
$query = "SELECT team_id,player_ids,comp_id FROM `event_rosters` WHERE id = $roster_id";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $comp_id=$row['comp_id'];
    $team_id=$row['team_id'];
    $player_ids = $row['player_ids'];
}

//get the team's UUID to match against players Team FSI ID
$query = "SELECT uuid, group_above_uuid FROM `teams` WHERE id = $team_id";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $uuid=$row['uuid'];
    $above_uuid=$row['group_above_uuid'];
}

//Get the players that have a Team FSI ID that matches our team's FSI ID
$players = array();
$query = "SELECT id FROM `players` WHERE team_uuid IN ('$uuid', '$above_uuid') ORDER BY lastname ASC";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $players[]=$row['id'];
}

//Get the roster limit from the comp info
$query = "SELECT max_event FROM `comps` WHERE id = $comp_id";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $max_event = $row['max_event'];
}

$options = array();

echo "<form name='erform' id='erform' />\n";

//Use our player ID's to get their names: lastname, firstname
foreach ($players as $player) {
    $options[$player]=rplayerName($player);
}

//Get options into alphebetical order
asort($options);

//cplayers is an array to hold current player ids which is string seperated by -
$cplayers = array();
$cplayers = explode('-', substr($player_ids, 1, (strlen($player_ids)-2)));

//Create select for each roster spot and provide an option for each player for team
for ($j=1;$j<=$max_event;$j++) {
    echo "<div class='row-fluid'>";
    echo "Player $j ";
    echo "<select id='p$j' class='chzn-select input-large' data-placeholder='Player $j'>\n";
    echo "<option value=''></option>\n";

    $i=1;
    foreach ($options as $option => $name) {
        if ($option==$cplayers[$j-1]) {$s='selected';} else {$s='';}
        echo "<option value=\"$option\" $s>$name</option>\n";
        $i++;
    }

    echo "</select></div><br/>\n";
}

echo "<input type='hidden' id='max' value='$max_event' />";
echo "<input type='hidden' id='roster_id' value='$roster_id' />";
echo "<input type='button' id='ersubmit' class='btn btn-primary' value='Update Roster' />";
echo "</form>\n";
