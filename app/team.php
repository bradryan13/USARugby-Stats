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
include_once './team_header.php';
include_once './team_games.php';

include_once './footer.php';
mysql_close(); ?>

</div>
</div>
