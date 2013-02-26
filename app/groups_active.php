<?php

include_once './config.php';
include_once './include.php';
use Source\APSource;

$teams = array();
$client = APSource::SessionSourceFactory();
if (isset($_POST['submit'])) {
    unset($_POST['post']);
    unset($_POST['check_all']);

    echo '<div class="alert alert-success">Selected groups hidden.</div>';

    $submit = $request->get('submit');
    if (strcmp($submit, 'Hide All') === 0) {
        $db->hideAllTeams();
    }
    elseif (strcmp($submit, 'Show All') === 0) {
        $db->showAllTeams();
    }
    elseif (strcmp($submit, 'Show Teams Only') === 0) {
        $db->showTeamType('team');
    }
    else {
        $teamid = $request->get('hideshowteamid');
        if (strcmp($submit, 'Hide') === 0) {
            $db->hideTeam($teamid);
        }
        elseif (strcmp($submit, 'Show') === 0) {
            $db->showTeam($teamid);
        }
    }
}

$teams = $db->getAllTeams();
?>

<h2>Group Management</h2>
<label class="flabel" for="check_all">Manage teams to display in competitions by checking the groups you would like to display as hidden.</label>

<form name="showhide" id="showhide" method="POST" action="">
  <input class="btn" name="submit" type="submit" value="Show All" />
  <input class="btn" name="submit" type="submit" value="Hide All" />
  <input class="btn" name="submit" type="submit" value="Show Teams Only" />
</form>
<hr />

<table>
  <?php
  foreach ($teams as $uuid => $team) {
      echo '<tr><td><form name="teams_showhide" id="teams_showhide" method="POST" action="">';
      if ($team['status'] == "hide") {
          echo "<input type='hidden' name='hideshowteamid' value='{$team['id']}'>";
          echo "<input class='btn' name='submit' type='submit' value='Show' />";
      }
      else {
          echo "<input type='hidden' name='hideshowteamid' value='{$team['id']}'>";
          echo "<input class='btn btn-primary' name='submit' type='submit' value='Hide' />";
      }
      $type = ucfirst($team['type']);
      echo "  <a href=\"team.php?id={$team['id']}\">{$team['name']}</a> - {$team['description']} - {$type} (<small><a href=\"{$config['auth_domain']}/groups/uuid/{$uuid}\">$uuid</a></small>)";
      echo '</form></td></tr>';
  }
  ?>
</table>

