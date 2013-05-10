<div id="wrapper" class="container-fluid">
  <div class="row-fluid">
    <form name='addscore' id='addscore' method='POST' action='' class="form-inline span11">
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>
      <div class="row-fluid">
        <div id="minute-wrapper" class="span1">
          <div class="control-group">
            <label for="minute" id="minute_label" class="control-label">Min.</label>
            <div class="controls">
              <input type="text" name="minute" id="minute" placeholder='Min' class='input-mini required' data-minute-max-value="121">
            </div>
          </div>
        </div>

        <div id="type-wrapper" class="span2">
          <div class="control-group">
            <label for="type" id="type_label" class="control-label">Type</label>
            <div class="controls">
               <select name='type' id='type' data-placeholder='Type' class='input-medium required chzn-select'>
                  <option value=''></option>
                  <option value='2'>Conversion</option>
                  <option value='4'>Drop Goal</option>
                  <option value='3'>Penalty Kick</option>
                  <option value='5'>Penalty Try</option>
                  <option value='1'>Try</option>
              </select>
            </div>
          </div>
        </div>

        <div id="player-wrapper" class="span3">
          <div class="control-group">
            <label for="player" id="player_label" class="control-label">Player</label>
            <div class="controls">
              <select name='player' data-placeholder='Player' id='player' class ="input-medium chzn-select">
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
          </div>
        </div>

        <div id="submit-wrapper" class="span2">
          <div class="control-group">
            <label for="submit" id="submit_label" class="control-label">&nbsp;</label>
            <div class="controls">
              <input type='hidden' name='refresh' id='refresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_score_events.php?id=$game_id"; ?>'>
              <input type='hidden' name='game_id' id='game_id' value='<?php echo "$game_id"; ?>'>
              <input type='submit' name='submit' class='button btn btn-primary' id='add_score' value='Add Score'>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
