 <?php
include_once './header.php';

echo "<h1>Game Roster</h1>";

$game_id = $_GET['gid'];
$team_id = $_GET['tid'];

//provide some game info
include_once './game_roster_info.php';
include './config.php';
$team = $db->getTeam($team_id);
Resque::setBackend('redis://redis:' . $config['redis_password'] . '@' . $config['redis_host']);
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
	echo '<script type="text/javascript">playerSyncPoll("' . $token . '", "", "' . $game_id . '", "' . $team_id . '");</script>';
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
        <span>Players synched.</span>
    </div>
<?php
}

//either allow edit by team/usarugby or just show for press / other teams
echo "<div id='groster'>";
if (editCheck(2, $team_id)) {
    //output names in lastname, firstname convention in dropdowns
    include_once './edit_game_roster.php';
} else {
    //output names in lastname, firstname convention
    include_once './game_player_sort.php';
}
echo "</div>";

mysql_close();
?>
