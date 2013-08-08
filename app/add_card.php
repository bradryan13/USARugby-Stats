<div class='span4'>
<div class='add-controls'>
<div class='section-header'><h3><span>Add Card</span></h3></div>
    <form name='addcard' id='addcard' method='POST' action=''>
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>
      
      		<div class="control">
                <input type="text" name="cardmin" id="cardmin" placeholder='Min' class='input-mini required' data-minute-max-value="121">
                <select name='cardtype' id='cardtype' data-placeholder="Card Type" class='required input-200p chzn-select'>
                  <option value=''></option>
                  <option value='22'>Red Card</option>
                  <option value='21' selected="selected">Yellow Card</option>
                </select>
	  		</div>
            
            <div class="control">
                <select name='cardplayer' id='cardplayer' data-placeholder="Player" class='required input-100 chzn-select'>
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
                 <input type='hidden' name='cardrefresh' id='cardrefresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_score_events.php?game_id=$game_id"; ?>'>
                  <input type='hidden' name='card_game_id' id='card_game_id' value='<?php echo "$game_id"; ?>'>
                 <input type='submit' name='submit' class='btn-flat btn-no red' id='add_card' value='Add Card'>
            </div>
    </form>
</div>
</div>

