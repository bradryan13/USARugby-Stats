<?php
include_once './include_micro.php';
$iframe = $request->get('iframe');
$team_uuid = $request->get('team_uuid');
include_once './include.php';

if (!empty($team_uuid)) {
	$team_id = $db->getSerialIDByUUID('teams', $team_uuid);
	$team = $db->getTeam($team_id);
	$game_rows = array();
}

if (empty($team_id)) {
	$team_id = $request->get('team_id');
}


?>
<div>
<script type="text/javascript" charset="utf-8">		
$(document).ready(function(){
  $('#sort-games').dataTable({
    "bAutoWidth": true,
    "bPaginate" : false,
    "aoColumnDefs": [
			{ 
				"bSortable": false, 
				 "aTargets": [ -1 ] // <-- gets last column and turns off sorting
			} ]
  })
/*
    .yadcf([
    {column_number : 0, data:["2013","2012"], filter_container_id: "external_filter_container", filter_default_label: "All Seasons", filter_reset_button_text: false}
    ]);
    $('.dataTables_filter input').attr('placeholder', 'Filter Games');
	$('div.dataTables_filter input').focus()
});
</script>

<div id="external_filter_container" class="no-clear-filter"></div>
	

  <?php
$team_games = $db->getTeamGames($team_id);
// If we're not looking at a team - we may be on a competition (top level)
//  related group - try to look it up.
if (empty($team_games)) {
	// team_uuid in this case would be comp_uuid
	$check_comp = 1;
	$team_games = $db->getCompetitionGames($team_uuid);
}
	else {
		$check_comp = 0;
}

if (empty($team_games)) {
	echo '<!-- No Games -->';
}
else {
	// Regular display.
	if (empty($iframe)) {
		echo "<table class='games-table' id='sort-games'><thead><th>Date</th><th>Home Team</th><th>Result/Time</th><th>Away Team</th><th>Type</th><th></th></thead>";
		foreach ($team_games as $team_game) {
			$game['status'] = $team_game['status'];
			$game['score'] = "{$team_game['home_score']} - {$team_game['away_score']}";
			$game['kickoff'] = date('l M d, Y', strtotime($team_game['kickoff']));
			$kickofftime = date('g:i A', strtotime($team_game['kickoff']));
			$kickoffdate = date('D, M d, Y', strtotime($team_game['kickoff']));
			$game['league'] = $team_game['league_type'];
			$base_url = $request->getScheme() . '://' . $request->getHost();
			$game_url = $base_url . "/" . "game.php?iframe=1&id=".$team_game['id']."&ops[0]=game_info&ops[1]=game_score&ops";
			$iframe_url = '<iframe height="2000px" width="100%" src="'.$game_url.'"></iframe>';
			echo "<tr>";
			echo "<td>" . $kickoffdate. "</td>";
			echo "<td class='home-team'>" . teamNameNL($team_game['home_id']) . "</td>";
			echo "<td class='score-time'><a href='game.php?id={$team_game['id']}'>";
			// If game score has not been entered, replace 0-0 with N/A
			if($team_game['away_score'] == "0" && $team_game['home_score'] == "0") {
				$game['score'] = "N/A";
			}
			// If game date has not passed, show game instead of score
			if (strtotime($kickoffdate) < time()) {
				echo $game['score'];
			}
			else {
				echo $kickofftime;
			}
			echo "</a></td>";
			echo "<td>" . teamNameNL($team_game['away_id']) . "</td>";
			echo "<td>". $game['league'] . "</td>";
			echo "<td>
					<div class='btn-group pull-right'>
						<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'><i class='icon-cog'></i><span class='caret'></span></a>
							<ul class='dropdown-menu'>
								<li><a href='game.php?id={$team_game['id']}'><i class='icon-edit'></i> Edit</a></li>
								<li><a href='#{$team_game['id']}' data-toggle='modal' class='red'>Game iFrame</a></li>
							</ul></div></td>";
			echo "</tr>";
			echo "<div id='{$team_game['id']}' class='modal hide fade' tabindex='-1'><div class='modal-header'><a href='#' class='close' data-dismiss='modal'>×</a><h3>Game iFrame</h3><div class='modal-body'><div class='divDialogElements'><input class='input-100' style='max-width: 482px;' id='xlInput' name='xlInput' value='$iframe_url' type='text'></div></div></div>";
			};
		echo "</table>";
	}
	// Iframe.
	else {
		if (!empty($iframe)) {
			$pre_date = '';
			foreach ($team_games as $team_game) {
			    $game['status'] = $team_game['status'];
				$game = array();
				$resource = $db->getResource($team_game['field_num']);
				$loc_url = getResourceMapUrl($resource);
				$game['comp_game_id'] = $team_game['comp_game_id'];
			    $kickofftime = date('g:i A', strtotime($team_game['kickoff']));
			    $kickoffdate = date('D, d M Y H:i:s', strtotime($team_game['kickoff']));
				$game['kickoff'] = date('l M d, Y', strtotime($team_game['kickoff']));
				$game['kickoff_time'] = date('g:ia', strtotime($team_game['kickoff']));
				if (!empty($team_game['field_loc']) && !empty($team_game['field_addr'])) {
				$game['location'] = "<a target='_blank' href='https://maps.google.com/?q=" . $team_game['field_addr'] . "'>" . $team_game['field_loc'] . " <span class='icon-map-marker'></span></a>";
				} else {
					$game['location'] = $team_game['field_loc'];
				}



			// If game date has not passed, show game instead of score
			date_default_timezone_set('America/Denver');
			$current_time = date('YMdHis', strtotime("+5 minutes"));
			$kickoffdate = date('YMdHis', strtotime($team_game['kickoff']));

			if ($current_time > $kickoffdate) { 
				$game['check_complete'] = 'Results';
				if ($team_game['status'] == 18) {
					$game['score'] = "<b><a target='_blank' href='game.php?iframe=1&id=" . $team_game['id'] . "'> {$team_game['home_score']} - {$team_game['away_score']} <i class='icon-time'></i></a></b>";
				} 
				elseif ($team_game['away_score'] == "0" && $team_game['home_score'] == "0") {
					$game['score'] = "<a target='_blank' href='game.php?iframe=1&id=" . $team_game['id'] . "'>N/A</a>";
				} else {
					$game['score'] = "<b><a target='_blank' href='game.php?iframe=1&id=" . $team_game['id'] . "'> {$team_game['home_score']} - {$team_game['away_score']} </a></b>";
				}
			}
			else {
				$game['score'] = $game['kickoff_time'];
				$game['check_complete'] = 'Upcoming Matches';
			}


				$game['away_id'] = teamNameNL($team_game['away_id']);
				$game['home_id'] = teamNameNL($team_game['home_id']);
				$game['pre_date'] = false;
				if ($pre_date != $game['kickoff']){
					$pre_date = $game['kickoff'];
					$game['pre_date'] = true;
				}
				else {
					$game['kickoff'] =  '';
				}
				$game_rows[] = $game;
			}
		}

		if (empty($twig)) {
			$loader = new Twig_Loader_Filesystem(__DIR__.'/views');
			$twig = new Twig_Environment($loader, array());
		}

		if ($check_comp == '1') {
		echo $twig->render('comp-games-iframe.twig', array('gamerows' => $game_rows));
		}
		else {
		echo $twig->render('comp-games.twig', array('gamerows' => $game_rows));
		}
	}
}
?>
</div>
<?php
    

include './footer.php';
