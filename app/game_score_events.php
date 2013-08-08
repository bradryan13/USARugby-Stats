<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}
?>


<script type="text/javascript" charset="utf-8">		
$(document).ready(function(){
  $('#events').dataTable({
    "bAutoWidth": true,
    "bPaginate": false,
    "oLanguage": {
    	"sEmptyTable":'No game events available.',
    	"sZeroRecords": 'No game events to display'},
    "bInfo": false,
    "aoColumnDefs": [
			{ 
				"bSortable": false, 
				 "aTargets": [ -1, -2, -3, -4 ] // <-- Turn off filtering for all but minute
			} ]
  })
   .yadcf([
    {column_number : 1, data:["Scores","Subs", "Cards"], filter_container_id: "external_filter_container", filter_default_label: "All Events", filter_reset_button_text: false}
    ]);
  $('.dataTables_filter input').attr('placeholder', 'Search');
});
</script>



<div id='external_filter_container'></div>
<?php

echo "<table class='table game-score-events' id='events'>";
	echo "<thead><th>Minute</th><th>Event</th><th>Team</th><th>Player</th>";
	if (editCheck() && empty($iframe)) { 
		echo "<th></th>"; }
	echo "</thead>";
$game_score_events = $db->getGameScoreEvents($game_id);
foreach ($game_score_events as $game_score_event) {
    echo "<tr class='points'><td>{$game_score_event['minute']}</td>\r";
    echo "<td><span class='scores'>Scores</span>".eType($game_score_event['type'])."</td>\r";
    echo "<td>".teamNameNL($game_score_event['team_id'], empty($iframe))."</td>\r";
    echo "<td>".playerName($game_score_event['player_id'], !empty($iframe), $game_id)."</td>\r";

    if (editCheck() && empty($iframe)) {
        echo "<td><form style='margin: 0; padding: 0' name='dForm{$game_score_event['id']}' id='dForm{$game_score_event['id']}'>";
        echo "<a href='#' class='dScore' id='dScore{$game_score_event['id']}' data-del-score-id='{$game_score_event['id']}'><i class='icon-trash'></i> Remove Score</a>";
        echo "<input type='hidden' class='dId' name='event_id' id='event_id' value='{$game_score_event['id']}' />";
        echo "<input type='hidden' name='refresh' id='refresh' value='game_score_events.php?id=$game_id' />";

        echo "</form></td>\r";
    }

    echo "</tr>\r";
}

$game_card_events = $db->getGameCardEvents($game_id);
foreach ($game_card_events as $game_card_event) {
    echo "<tr><td>{$game_card_event['minute']}</td>";
    echo "<td><span class='cards'>Cards</span>".eType($game_card_event['type'])."</td>";
    echo "<td>".teamNameNL($game_card_event['team_id'], empty($iframe))."</td>";
    echo "<td>".playerName($game_card_event['player_id'], !empty($iframe), $game_id)."</td>";

    if (editCheck() && empty($iframe)) {
        echo "<td><form style='margin: 0; padding: 0' name='dForm{$game_card_event['id']}' id='dForm{$game_card_event['id']}'>";
        echo "<a href='#' class='dCard' id='dCard{$game_card_event['id']}' data-del-card-id='{$game_card_event['id']}'> <i class='icon-trash'></i> Remove Card</a>";
        echo "<input type='hidden' class='dId' name='event_id' id='event_id' value='{$game_card_event['id']}' />";
        echo "<input type='hidden' name='refresh' id='refresh' value='game_score_events.php?game_id=$game_id' />";

        echo "</form></td>\r";
    }

}

$game_sub_events = $db->getGameSubEvents($game_id);
foreach ($game_sub_events as $game_sub_event) {
    echo "<tr><td>{$game_sub_event['minute']}</td>";
    echo "<td><span class='subs'>Subs</span>".eType($game_sub_event['type'])."</td>";
    echo "<td>".teamNameNL($game_sub_event['team_id'], empty($iframe))."</td>";
    echo "<td>".playerName($game_sub_event['player_id'], !empty($iframe), $game_id)."</td>";

    if (editCheck() && empty($iframe)) {
        echo "<td><form style='margin: 0; padding: 0' name='dForm{$game_sub_event['id']}' id='dForm{$game_sub_event['id']}'>";
        echo "<a href='#' class='dSub' id='dSub{$game_sub_event['id']}' data-del-sub-id='{$game_sub_event['id']}'> <i class='icon-trash'></i> Remove Sub</a>";
        echo "<input type='hidden' class='dId' name='event_id' id='event_id' value='{$game_sub_event['id']}' />";
        echo "<input type='hidden' name='subDrefresh' id='subDrefresh' value='game_score_events.php?game_id=$game_id' />";

        echo "</form></td>\r";
    }

}




echo "</table>\r";
