<?php
include_once './include_mini.php';

if (empty($game_id)) {
    if (!$game_id = $request->get('id')) {
        $game_id = $request->get('game_id');
    }
}

$game = $db->getGame($game_id);
$home_id = $game['home_id'];
$away_id = $game['away_id'];

?>

<div class="header meta">
	<div class="container">
<?php
echo "<div class='scoreboard'>"	;
echo "<div class='away-team'><h1>" .teamNameNL($away_id, empty($iframe))."</h1></div>";
echo "<div class='score'>{$game['away_score']} -  {$game['home_score']}</div>";
echo "<div class='home-team'><h1>" .teamNameNL($home_id, empty($iframe))."</h1></div>";
echo "</div></div></div>"
?>


