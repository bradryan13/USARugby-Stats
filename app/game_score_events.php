<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}

echo "<table class='table game-score-events'>";
	echo "<tr><th>Minute</th><th>Score</th><th>Team</th><th>Player</th><th></th></tr>";


$game_score_events = $db->getGameScoreEvents($game_id);
if (!empty($iframe)) {
  echo get_header_string('game_events');
}
foreach ($game_score_events as $game_score_event) {
    echo "<tr><td>{$game_score_event['minute']}'</td>\r";
    echo "<td>".eType($game_score_event['type'])."</td>\r";
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

echo "</table>\r";
