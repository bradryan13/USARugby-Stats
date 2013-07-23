<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}

echo "<table class='table events-sub'>";

$game_sub_events = $db->getGameSubEvents($game_id);
if (!empty($iframe)) {
  echo get_header_string('game_events');
}
foreach ($game_sub_events as $game_sub_event) {
    echo "<tr><td>{$game_sub_event['minute']}'</td>";
    echo "<td>".eType($game_sub_event['type'])."</td>";
    echo "<td>".teamNameNL($game_sub_event['team_id'], empty($iframe))."</td>";
    echo "<td>".playerName($game_sub_event['player_id'], !empty($iframe), $game_id)."</td>";

    if (editCheck() && empty($iframe)) {
        echo "<td><form style='margin: 0; padding: 0' name='dForm{$game_sub_event['id']}' id='dForm{$game_sub_event['id']}'>";
        echo "<a href='#' class='dSub' id='dSub{$game_sub_event['id']}' data-del-sub-id='{$game_sub_event['id']}'> <i class='icon-trash'></i></a>";
        echo "<input type='hidden' class='dId' name='event_id' id='event_id' value='{$game_sub_event['id']}' />";
        echo "<input type='hidden' name='subDrefresh' id='subDrefresh' value='game_sub_events.php?game_id=$game_id' />";

        echo "</form></td>\r";
    }

}

echo "</table>";
