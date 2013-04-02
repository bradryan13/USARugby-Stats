<?php
include_once './config.php';
include_once './header.php';
include_once './include_mini.php';

$roster_id = $request->get('id');
$roster_id = (empty($roster_id)) ? $_GET['roster_id'] : $roster_id;

//Get info about our event and roster
$roster = $db->getRosterById($roster_id);
$comp_id = $roster['comp_id'];
$team_id = $roster['team_id'];


$team = $db->getTeam($team_id);
Resque::setBackend($config['redis_host']);
Resque::redis()->auth($config['redis_password']);
if (!empty($_GET['sync_roles'])) {
	$players = $db->getTeamPlayers($team['uuid']);
	$tokens = array();
	foreach ($players as $player) {
		$token = Resque::enqueue('get_player_roles', 'GetPlayerRoles', array('team_uuid' => $team['uuid'], 'player_uuid' => $player['uuid']), true);
		$tokens[$player['id']] = $token;
	}
	?>
	<script type="text/javascript">playerRoleSyncPoll(<?php echo json_encode($tokens) . ',' . $roster_id; ?>);</script>
	<div id="synching">
                <span>Synching roles...</span>
                <div class="progress progress-striped active">
                        <div class="bar" style="width: 0%;"></div>
                </div>
        </div>
	<?php
}
elseif (empty($_GET['no_sync'])) {
	$token = Resque::enqueue('get_team_players', 'GetTeamPlayers', array('team_uuid' => $team['uuid']), true);
	echo '<script type="text/javascript">playerSyncPoll("' . $token . '", ' . $roster_id . ');</script>';
	?>
	<div id="synching">
		<span>Synching players...</span>
		<div class="progress progress-striped active">
			<div class="bar" style="width: 0%;"></div>
		</div>
	</div>
<?php
}
else {
     ?>
    <div id="synching">
        <span>Players and roles synched.</span>
    </div>
<?php
}
?>
<div class='page-header'>
    <h1>Event Roster</h1>
    <h2><?php echo compName($roster_id); ?> </h2>
    <h3><?php echo teamName($team_id); ?> </h3>
</div>
<?php

//either allow edit by team/usarugby or just show for press / other teams
echo "<div id='eroster'>";
//to allow the teams to edit, make it editCheck(1,$team_id)
if (editCheck(1)) {
    //output names in lastname, firstname convention in dropdowns
    include_once './edit_event_roster.php';
} else {
    //output names in lastname, firstname convention
    include_once './player_sort.php';
}
echo "</div>";

mysql_close();
