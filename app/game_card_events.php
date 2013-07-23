<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}

echo "<table class='table card-events'>";

$game_card_events = $db->getGameCardEvents($game_id);
if (!empty($iframe)) {
  echo get_header_string('game_events');
}
foreach ($game_card_events as $game_card_event) {
    echo "<tr><td>{$game_card_event['minute']}'</td>";
    echo "<td>".eType($game_card_event['type'])."</td>";
    echo "<td>".teamNameNL($game_card_event['team_id'], empty($iframe))."</td>";
    echo "<td>".playerName($game_card_event['player_id'], !empty($iframe), $game_id)."</td>";

    if (editCheck() && empty($iframe)) {
        echo "<td><form style='margin: 0; padding: 0' name='dForm{$game_card_event['id']}' id='dForm{$game_card_event['id']}'>";
        echo "<a href='#' class='dCard' id='dCard{$game_card_event['id']}' data-del-card-id='{$game_card_event['id']}'> <i class='icon-trash'></i></a>";
        echo "<input type='hidden' class='dId' name='event_id' id='event_id' value='{$game_card_event['id']}' />";
        echo "<input type='hidden' name='refresh' id='refresh' value='game_card_events.php?game_id=$game_id' />";

        echo "</form></td>\r";
    }

}

echo "</table>";
