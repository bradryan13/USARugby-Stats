<?php
include_once './include_mini.php';


if (empty($game)) {
  $game_id = empty($game_id) ? $request->get('id') : $game_id;
  $game = $db->getGame($game_id);
}


?>
<div class="game-meta" id="game-id"><div class="container"><ul>
<?php
echo "<li>". compName($game['comp_id'], empty($iframe))."</li>";
echo "<li>". date('F j, Y', strtotime($game['kickoff']))."</li>";
echo "<li>Kickoff: ".date('g:i', strtotime($game['kickoff']))."</li>";

if (!empty($game['field_num'])) {
    $resource = $db->getResource($game['field_num']);
    $loc_url = getResourceMapUrl($resource);
    if (!empty($loc_url)) {
        echo "<li> Field: ". $resource['title'] . " (<a href='$loc_url' target='_blank'>Map</a>)</li>";
    }
    else {
        echo "<li> Field: ". $game['field_num'] . "</li>";
    }
}


if (editCheck() && empty($iframe)) {
    echo "<input type='button' class='btn-no' id='eShow' name='eShow' value='Edit Game' />";
    echo "<input type='hidden' id='game_id' value='$game_id' />";
}
?>
</ul></div></div>


