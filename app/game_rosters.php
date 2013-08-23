<?php

include './include_micro.php';
//get the home players and numbers
$home_roster = $db->getRoster($game_id, $home_id);
$home_plyrs = $home_roster['player_ids'];
$home_positions = $home_roster['positions'];
$home_nums = $home_roster['numbers'];
$home_frs = $home_roster['frontrows'];


//get the away players and numbers
$away_roster = $db->getRoster($game_id, $away_id);
$away_plyrs = $away_roster['player_ids'];
$away_positions = $away_roster['positions'];
$away_nums = $away_roster['numbers'];
$away_frs = $away_roster['frontrows'];


//split up the player ids from the long - string
$homeps = array_filter(explode('-', $home_plyrs));
$awayps = array_filter(explode('-', $away_plyrs));
$home_positions = array_filter(explode('-', $home_positions));
$away_positions = array_filter(explode('-', $away_positions));
$homens = array_filter(explode('-', $home_nums));
$awayns = array_filter(explode('-', $away_nums));
$homefrs = array_filter(explode('-', $home_frs));
$awayfrs = array_filter(explode('-', $away_frs));
$positions = getPositionList('display');

//find which has more to limit our roster output
if (count($homeps) > count($awayps)) {
    $max = count($homeps);
} else {
    $max = count($awayps);
}
?>
<div class="row-fluid game-rosters-intro">
<div class="span6">

<?php
echo "<table class='table rosters'>";
$link = empty($iframe);
echo "<thead><tr><th>#</th><th>POS</th><th>" . teamNameNL($away_id, $link) . "</th><th class='frout'>FR</th></tr></thead>";

//0 element has been filtered above so start at 1
//displaying number, name, FR capable
for ($i=1; $i<=$max; $i++) {
    echo "<tr><td class='player-num'>{$awayns[$i]}</td>\r";
    echo "<td class='player-pos'>";
    if (!empty($away_positions[$i])) {
        echo($positions[$away_positions[$i]]);
    }
    echo "</td>";
    $away_player_name_string = empty($awayps[$i]) ? "<td class='player-name'>&nbsp;</td>\r" : "<td class='player-name'>".playerName($awayps[$i], !$link, $game_id)."</td>\r";
    echo $away_player_name_string;
    if (isset($awayfrs[$i]) && $awayfrs[$i]==1) {$frout='FR';} else {$frout='';}
    echo "<td class='frout'>$frout</td>\r";
    echo "</tr>";
}

if (editCheck() && empty($iframe)) {
    echo "<td class='edit-rosters-intro' colspan='4'><a class='left' href='game_roster.php?gid=$game_id&tid=$away_id'>Edit Roster</a></td>\n";
}

echo "</table>";
?>
</div><div class="span6">


<?php
echo "<table class='table rosters'>";
$link = empty($iframe);
echo "<thead><tr><th>#</th><th>POS</th><th>" . teamNameNL($home_id, $link) . "</th><th class='frout'>FR</th></tr></thead>";

//0 element has been filtered above so start at 1
//displaying number, name, FR capable
for ($i=1; $i<=$max; $i++) {
    echo "<tr><td class='player-num'>{$homens[$i]}</td>\r";
    echo "<td class='player-pos'>";
    if (!empty($away_positions[$i])) {
        echo($positions[$away_positions[$i]]);
    }
    echo "</td>";
    $home_player_name_string = empty($homeps[$i]) ? "<td class='player-name'>&nbsp;</td>\r" : "<td class='player-name'>".playerName($homeps[$i], !$link, $game_id)."</td>\r";
    echo $home_player_name_string;
    if (isset($homefrs[$i]) && $homefrs[$i]==1) {$frout='FR';} else {$frout='';}
    echo "<td class='frout'>$frout</td>\r";
    echo "</tr>";
}

if (editCheck() && empty($iframe)) {
    echo "<td class='edit-rosters-intro' colspan='4'><a class='left' href='game_roster.php?gid=$game_id&tid=$home_id'>Edit Roster</a></td>\n";
}

echo "</table>";
?>
</div></div>
