<?php
include_once './include_mini.php';

$comp_id = $_GET['id'];
?>
<div id="wrapper" class="container-fluid">
  <div class="row-fluid">
    <form name='addgame' id='addgame' method='POST' action='' class="form-inline">
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>

      <div class="row-fluid">

        <div id="game-wrapper" class="span1">
          <div class="control-group">
            <label for="gnum" id="gnum_label" class="control-label">Game #</label>
            <div class="controls">
               <input id='gnum' name='gnum' type='text' size='1' class="input-small required" placeholder="Game #">
            </div>
          </div>
        </div>


        <div id="game-wrapper" class="span1">
          <div class="control-group">
            <label for="field_loc" id="field_loc_label" class="control-label">Field Name</label>
            <div class="controls">
               <input id='field_loc' name='fied_loc' type='text' size='50' class="input-medium" placeholder="Field Location">
            </div>
          </div>
        </div>

        <div id="game-wrapper" class="span1">
          <div class="control-group">
            <label for="field_addr" id="field_addr_label" class="control-label">Field Address</label>
            <div class="controls">
               <input id='field_addr' name='field_addr' type='text' size='100' class="input-medium" placeholder="Field Address">
            </div>
          </div>
        </div>

        <div id="date-wrapper" class="span1">
          <div class="control-group">
            <label for="kdate" id="kdate_label" class="control-label">Start Date</label>
            <div class="controls">
               <?php
                // Determine date stard and end.
                $comp = $db->getCompetition($comp_id);
                $game_earliest = $comp['start_date'];
                $game_latest = $comp['end_date'];
                echo "<input id='kdate' name='kdate' type='text' size='10' class='date_select input-small required' data-date-startdate='$game_earliest' data-date-enddate='$game_latest' placeholder='Date'>"
               ?>
            </div>
          </div>
        </div>

        <div id="time-wrapper" class="span1">
          <div class="control-group">
             <label for="ko_time" id="ko_time_label" class="control-label">Time</label>
            <div class="controls">
               <input name="ko_time" id="ko_time" type="text" size="2" class=" input-small time-entry required" placeholder='Time'>
            </div>
          </div>
        </div>

        <div id="home-wrapper" class="span2">
          <div class="control-group">
            <label for="home" id="home_label" class="control-label">Home Team</label>
            <div class="controls">
             <select data-placeholder='Home Team' name='home' id='home' class="input-medium required chzn-select addgame-home">
              <option value=''></option>
                <?php
                $query = "SELECT * FROM `ct_pairs` WHERE comp_id = $comp_id";
                $result = mysql_query($query);
                while ($row=mysql_fetch_assoc($result)) {

                    $query1 = "SELECT * FROM `teams` WHERE id = {$row['team_id']} ORDER BY name ASC";
                    $result1 = mysql_query($query1);
                    while ($row1=mysql_fetch_assoc($result1)) {
                        echo "<option value='{$row1['uuid']}'>{$row1['name']}</option>";
                    }
                }
                ?>
              </select>
            </div>
          </div>
        </div>

        <div id="away-wrapper" class="span2">
          <div class="control-group">
            <label for="away" id="away_label" class="control-label">Away Team</label>
            <div class="controls">
               <select data-placeholder='Away Team' name='away' id='away' class="required input-medium chzn-select addgame-away">
                  <option value=''></option>

                  <?php
                  $query = "SELECT * FROM `ct_pairs` WHERE comp_id = $comp_id";
                  $result = mysql_query($query);
                  while ($row=mysql_fetch_assoc($result)) {

                      $query1 = "SELECT * FROM `teams` WHERE id = {$row['team_id']} ORDER BY name ASC";
                      $result1 = mysql_query($query1);
                      while ($row1=mysql_fetch_assoc($result1)) {
                          echo "<option value='{$row1['uuid']}'>{$row1['name']}</option>";
                      }
                  }
                  ?>

              </select>
            </div>
          </div>
        </div>
      
        <div id="submit-wrapper" class="span1">
          <div class="control-group">
            <label for="submit" id="submit_label" class="control-label">&nbsp;</label>
            <div class="controls">
              <input type='hidden' name='grefresh' id='grefresh' value='<?php echo "comp_games.php?id=$comp_id"; ?>'>
              <input type='hidden' name='active-domain' id='active-domain' value='<?php echo $_SESSION['_sf2_attributes']['domain'] ?>'>
              <input type='hidden' name='comp_id' id='comp_id' value='<?php echo $comp_id; ?>'>
              <input type='submit' name='submit' class='button btn btn-primary' id='add_game' value='Add Game'>
            </div>
          </div>
        </div>
      </div>
    </form>
</div>
</div>

<!--this is to hide the errors after a jquery refresh upon adding a new team to the comp-->
<script type='text/javascript'>$('.error').not(function(index){return $(this).hasClass('control-group');}).hide();</script>
<script>
      $(function(){
        $("#field_addr").geocomplete()
      });
    </script>
<?php
include_once './footer.php';
