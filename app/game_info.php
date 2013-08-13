<?php
include_once './include_mini.php';

$game_id = empty($game_id) ? $request->get('id') : $game_id;
$base_url = $request->getScheme() . '://' . $request->getHost();
$game_url= $base_url . "/" . "game.php?iframe=1&id=".$game_id."&ops[0]=game_info&ops[1]=game_score&ops";
$iframe_url = "<iframe height='2000px' width='100%' src='".$game_url."'></iframe>"; ?>

<div id="iframe-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
				<div class="modal-header">
					<a href="#" class="close" data-dismiss="modal">×</a>
									<h3>Game iFrame </h3>
					</div>
				<div class="modal-body">
					<div class="divDialogElements">
						<input class="input-100" style="max-width: 512px;" id="xlInput" name="xlInput" value="<?php echo $iframe_url ?>" type="text">
						</div>
					</div>
</div>


<?php

if (empty($game)) {
  $game_id = empty($game_id) ? $request->get('id') : $game_id;
  $game = $db->getGame($game_id);
}
?>
<div class="game-meta" id="game-id"><div class="container"><ul>
<?php
echo "<li>". compNameNL($game['comp_id'], empty($iframe))."</li>";
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
if (empty($iframe)) {
echo '<li><a href="#iframe-modal" data-toggle="modal" class="red">Game iFrame</a></li>';
}

if (editCheck() && empty($iframe)) {
    echo "<input type='button' class='btn-no' id='eShow' name='eShow' value='Edit Game' />";
    echo "<input type='hidden' id='game_id' value='$game_id' />";
}
?>
</ul></div></div>


