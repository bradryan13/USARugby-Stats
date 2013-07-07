<?php
use Source\Job\GroupMembersSyncJob;

include_once './include.php';

$team_id = $request->get('id');

$team = $db->getTeam($team_id);
$team_logo = getFullImageUrl($team['logo_url']);

if ($request->get('refresh')) {
    $attr = $_SESSION['_sf2_attributes'];
    $groupmembersync = new GroupMembersSyncJob($attr, $team['uuid']);
    $groupmembersync->run();
    if (!empty($team['group_above_uuid'])) {
        $groupmembersync = new GroupMembersSyncJob($attr, $team['group_above_uuid']);
        $groupmembersync->run();
    }
}
?>
<div class="header">
	<div class="container">
	<div class="team-logo">
				<!-- Problems with AllPlayers SSL Certificate  -->
				<?php echo "<img src='$team_logo' alt='{$team['name']}'/>";  ?>
	</div>
    <div class="team-name">
        <?php echo "<h1 class='w-logo'>{$team['name']}</h1>"; ?>
    </div>
	</div>
</div>

<div class="container">
<div id="maincontent">

<?php


/* 
echo "<h2>Event Rosters</h2>";
//Get the rosters for this team
include_once './team_event_rosters.php';
echo "<br/>";
*/

//Get the Games for this team
include_once './team_games.php';

/*
echo "<h2>Game Rosters</h2>";
//Get the rosters for this team
echo "<div class='rosters-wrapper'>";
include_once './team_game_rosters.php';
echo "<br/>";
echo "</div>";
*/

include_once './footer.php';
mysql_close(); ?>

</div>
</div>
