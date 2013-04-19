<div id="wrapper" class="container-fluid">
  <div class="row-fluid">
    <form name='addcard' id='addcard' method='POST' action=''>
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>
      <div class="row-fluid">

          <div id="cardmin-wrapper" class="span1">
            <div class="control-group">
              <label for="cardmin" id="cardmin_label" class="control-label">Min.</label>
              <div class="controls">
                <input type="text" name="cardmin" id="cardmin" placeholder='Min' class='input-mini required' data-minute-max-value="121">
              </div>
            </div>
          </div>


          <div id="cardtype-wrapper" class="span2">
            <div class="control-group">
              <label for="cardtype" id="cardtype_label" class="control-label">Type</label>
              <div class="controls">
                <select name='cardtype' id='cardtype' data-placeholder="Card Type" class='required input-medium chzn-select'>
                  <option value=''></option>
                  <option value='22'>Red Card</option>
                  <option value='21'>Yellow Card</option>
                </select>
              </div>
            </div>
          </div>

          <div id="cardplayer-wrapper" class="span2">
            <div class="control-group">
              <label for="cardplayer" id="cardplayer_label" class="control-label">Player</label>
              <div class="controls">
                <select name='cardplayer' id='cardplayer' data-placeholder="Player" class='required input-medium chzn-select'>
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
                 <input type='hidden' name='cardrefresh' id='cardrefresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/game_card_events.php?game_id=$game_id"; ?>'>
                  <input type='hidden' name='card_game_id' id='card_game_id' value='<?php echo "$game_id"; ?>'>
                 <input type='submit' name='submit' class='button btn btn-primary' id='add_card' value='Add Card'>
              </div>
            </div>
          </div>
        <div> <!--row-fluid-->
    </form>
  </div>
</div>

