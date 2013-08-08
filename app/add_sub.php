<div class='span4'>
<div class='add-controls'>
<div class='section-header'><h3><span>Add Sub</span></h3></div>
    <form name='addsub' id='addsub' method='POST' action=''>
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>

	  		 <div class="control">
              <input type="text" name="submin" id="submin" placeholder='Min' class='input-mini required' data-minute-max-value="121">
         

              <select name='subtype' id='subtype' data-placeholder='Sub Type' class='chzn-select input-200p required'>
                <option value=''></option>
                <option value='15'>Blood</option>
                <option value='13'>Injury</option>
                <option value='17'>Front Row Card</option>
                <option value='11' selected="selected">Tactical</option>
              </select>
     		 </div>
         		 <div class="control">

    
                <select name='player_on' id='player_on' data-placeholder="Player On" class='input-100 required chzn-select'>
                  <option value=""></option>
                <?php
                  echo "<optgroup label='" . teamName($away_id, FALSE) ."'>";
                  foreach ($awayps as $awayp) {
                    if ($awayp != 'NIL') {
                      echo "<option value='$awayp'>#" . playerNumber($game_id, $away_id, $awayp) ." " . playerName($awayp) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                  echo "<optgroup label='" . teamName($home_id, FALSE) ."'>";
                  foreach ($homeps as $homep) {
                    if ($homep != 'NIL') {
                      echo "<option value='$homep'>#" . playerNumber($game_id, $home_id, $homep) ." " . playerName($homep) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                ?>
              </select>
         		 </div>
      		 <div class="control">

    
              <select name='player_off' id='player_off' data-placeholder="Player Off" class='input-100 required chzn-select'>
                <option value=""></option>
                <?php
                  echo "<optgroup label='" . teamName($away_id, FALSE) ."'>";
                  foreach ($awayps as $awayp) {
                    if ($awayp != 'NIL') {
                      echo "<option value='$awayp'>#" . playerNumber($game_id, $away_id, $awayp) . " " . playerName($awayp) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                  echo "<optgroup label='" . teamName($home_id, FALSE) ."'>";
                  foreach ($homeps as $homep) {
                    if ($homep != 'NIL') {
                      echo "<option value='$homep'>#" . playerNumber($game_id, $home_id, $homep) ." " . playerName($homep) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                ?>
              </select>
      		 </div>
           
		 <div class="control">

                <input type='hidden' name='subrefresh' id='subrefresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_score_events.php?game_id=$game_id"; ?>'>
                <input type='hidden' name='sub_game_id' id='sub_game_id' value=<?php echo "$game_id"; ?>>
                <input type='submit' name='submit' class='btn-flat btn-no red' id='add_sub' value='Add Sub'>
		 </div>
          
    </form>
</div>
  </div>
