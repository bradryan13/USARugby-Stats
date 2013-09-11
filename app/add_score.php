<div class='span4'>
<div class='add-controls'>
<div class='section-header'><h3><span>Add Score</span></h3></div>
<form name='addscore' id='addscore' method='POST' action=''>
	<div class="alert error alert-error" id="form-validation-error">
     	<button type="button" class="close" data-dismiss="alert">×</button> <div class="error-message"></div>
	</div>
		 <div class="control clearfix">
		 	<div class="min">
	         	<input type="text" name="minute" id="minute" placeholder='Min' class='input-mini required' data-minute-max-value="121">
	         </div>
	         <div class="action-type">
	         	<select name='type' id='type' data-placeholder='Score Type' selected='Conversion' class='input-200p required chzn-select'>
	                  <option value=''></option>
	                  <option value='2' selected="selected">Conversion</option>
	                  <option value='4'>Drop Goal</option>
	                  <option value='3'>Penalty Kick</option>
	                  <option value='5'>Penalty Try</option>
	                  <option value='1'>Try</option>
	              </select>
			 </div>
			 </div>
              <div class="control">
              <select name='player' data-placeholder='Player' id='player' class ="input-100 chzn-select">
				              <?php
				echo "<option value='team$away_id'>--".teamName($away_id)."--</option>";
				foreach ($awayps as $awayp) {
					if ($awayp != 'NIL') {
						echo "<option value='$awayp'>#" . playerNumber($game_id, $away_id, $awayp) ." " . playerName($awayp) . "</option>";
					}
				}
				echo "<option value='team$home_id'>--".teamName($home_id)."--</option>";
				foreach ($homeps as $homep) {
					if ($homep != 'NIL') {
						echo "<option value='$homep'>#" . playerNumber($game_id, $home_id, $homep) ." " . playerName($homep) . "</option>";
					}
				}
				?>
              </select>
			  </div>
			  <div class="control">
	              <input type='hidden' name='refresh' id='refresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_score_events.php?id=$game_id"; ?>'>
	              <input type='hidden' name='game_id' id='game_id' value='<?php echo "$game_id"; ?>'>
	              <input type='submit' name='submit' class='btn-flat btn-no red' id='add_score' value='Add Score'>
			  </div>
</form>
</div>
</div>
