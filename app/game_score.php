<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}

$game = $db->getGame($game_id);
echo "{$game['away_score']} - {$game['home_score']}";
