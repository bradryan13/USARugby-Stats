<?php
use Source\QueueHelper;

include_once './include.php';
use Source\APSource;

$teams = array();
if ($oauth_disabled) {
    $client = APSource::BasicAuthFactory($_SESSION['user'], $_SESSION['password']);
}
else {
    $client = APSource::SessionSourceFactory();
}
$teams = $db->getAllTeams();

if (isset($_POST['submit'])) {
    unset($_POST['submit']);
    $qh = new QueueHelper();
    echo '<div class="alert alert-success">Players sync enqueued.</div>';

    foreach ($_POST as $name => $value) {
        if ($name == 'sync_all' && $value == 'on') {
            $qh->GroupMembersSync();
        }
        elseif ($uuid = split('_', $name)) {
            $qh->GroupMembersSync($uuid[1]);
        }
    }
}
?>
<form name="teams_sync" id="teams_sync" method="POST" action="">
    <h2>Update Players in Groups</h2>
    <label class="flabel" for="sync_all">Update players in groups by checking the groups you would synchronize.</label>
    <input type="checkbox" name="sync_all" id="sync_all" /><span>Select All</span>
    <input class="btn btn-warning btntop" name="submit" type="submit" value="Sync Team(s)" />
    <br />
    <?php
    foreach ($teams as $uuid => $team) {
        echo '<label class=\"checkbox\">';
        echo "<input class='grps-active' type=\"checkbox\" name=\"team_$uuid\" \\>";
        echo "  {$team['name']} (<small>$uuid</small>)";
        echo '</label>';
    }
    ?>
    <input class="btn btn-warning btntop" name="submit" type="submit" value="Sync Team(s)" />
</form>
