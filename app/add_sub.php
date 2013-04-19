<div id="wrapper" class="container-fluid">
  <div class="row-fluid">
    <form name='addsub' id='addsub' method='POST' action=''>
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>

      <div class="row-fluid">
        <div id="submin-wrapper" class="span1">
          <div class="control-group">
            <label for="submin" id="submin_label" class="control-label">Min.</label>
            <div class="controls">
              <input type="text" name="submin" id="submin" placeholder='Min' class='input-mini required' data-minute-max-value="121">
            </div>
          </div>
        </div>

        <div id="subtype-wrapper" class="span2">
          <div class="control-group">
            <label for="subtype" id="subtype_label" class="control-label">Type</label>
            <div class="controls">
              <select name='subtype' id='subtype' data-placeholder='Type' class='chzn-select input-medium required'>
                <option value=''></option>
                <option value='15'>Blood</option>
                <option value='13'>Injury</option>
                <option value='17'>Front Row Card</option>
                <option value='11'>Tactical</option>
              </select>
            </div>
          </div>
        </div>

        <div id="player_on-wrapper" class="span2">
          <div class="control-group">
           <label for="player_on" id="player_on_label" class="control-label">Player On</label>
            <div class="controls">
                <select name='player_on' id='player_on' data-placeholder="Player On" class='input-medium required chzn-select'>
                  <option value=""></option>
                <?php
                  echo "<optgroup label='" . teamName($away_id, FALSE) ."'>";
                  foreach ($awayps as $awayp) {
                    if ($awayp != 'NIL') {
                      echo "<option value='$awayp'>".playerName($awayp)." #" . playerNumber($game_id, $away_id, $awayp) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                  echo "<optgroup label='" . teamName($home_id, FALSE) ."'>";
                  foreach ($homeps as $homep) {
                    if ($homep != 'NIL') {
                      echo "<option value='$homep'>".playerName($homep)." #" . playerNumber($game_id, $home_id, $homep) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                ?>
              </select>
            </div>
          </div>
        </div>

        <div id="player_off-wrapper" class="span2">
          <div class="control-group">
            <label for="player_off" id="player_off_label" class="control-label">Player Off</label>
            <div class="controls">
              <select name='player_off' id='player_off' data-placeholder="Player Off" class='input-medium required chzn-select'>
                <option value=""></option>
                <?php
                  echo "<optgroup label='" . teamName($away_id, FALSE) ."'>";
                  foreach ($awayps as $awayp) {
                    if ($awayp != 'NIL') {
                      echo "<option value='$awayp'>".playerName($awayp)." #" . playerNumber($game_id, $away_id, $awayp) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                  echo "<optgroup label='" . teamName($home_id, FALSE) ."'>";
                  foreach ($homeps as $homep) {
                    if ($homep != 'NIL') {
                      echo "<option value='$homep'>".playerName($homep)." #" . playerNumber($game_id, $home_id, $homep) . "</option>";
                    }
                  }
                  echo "</optgroup>";
                ?>
              </select>
            </div>
          </div>
        </div>

        <div id="submit-wrapper" class="span1">
            <div class="control-group">
              <label for="submit" id="submit_label" class="control-label">&nbsp;</label>
              <div class="controls">
                <input type='hidden' name='subrefresh' id='subrefresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_sub_events.php?game_id=$game_id"; ?>'>
                <input type='hidden' name='sub_game_id' id='sub_game_id' value=<?php echo "$game_id"; ?>>
                <input type='submit' name='submit' class='button btn btn-primary' id='add_sub' value='Add Sub'>
              </div>
            </div>
          </div>

      </div>
    </form>
  </div>
</div>
