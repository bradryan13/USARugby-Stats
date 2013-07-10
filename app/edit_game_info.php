<?php
include_once './include_mini.php';

if (!isset($game_id) || !$game_id) {$game_id=$_GET['id'];}

//get info for the game with id in url
$game = $db->getGame($game_id);
$comp = $db->getCompetition($game['comp_id']);
$kickoff = new DateTime($game['kickoff']);

?>
<div class="game-meta" id="game-id"><div class="container"><ul>  
    <form name='editgame' id='editgame' method='POST' action='' class="form-inline">
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>
      <div class="row-fluid">
	     <li>
        <div id="comp-wrapper">
          <div class="control-group">
            <label for="comp" id="comp_label" class="control-label">Comp</label>
            <div class="controls">
               <?php echo compName($game['comp_id']); ?>
            </div>
          </div>
        </div>


         </li>
         
         
         <li>
        <div id="date-wrapper">
          <div class="control-group">
            <label for="kdate" id="kdate_label" class="control-label">Date</label>
            <div class="controls">
               <?php
                // Determine date start. and end.
                $game_earliest = $comp['start_date'];
                $game_latest = $comp['end_date'];
                $value = $kickoff->format('Y-m-d');
                echo "<input id='kdate' name='kdate' type='text' value='$value' size='10' class='date_select input-small required' data-date-startdate='$game_earliest' data-date-enddate='$game_latest' placeholder='Date'>"
               ?>
            </div>
          </div>
        </div>

         </li>
         
         
          
         <li>
        <div id="time-wrapper">
          <div class="control-group">
            <label for="ko_time" id="ko_time_label" class="control-label">Kickoff</label>
            <div class="controls">
              <?php
                  $value = $kickoff->format('h:iA');
                  echo "<input name='ko_time' id='ko_time' value='$value' type='text' size='2' class='input-small time-entry required' placeholder='Time'>";
              ?>
            </div>
          </div>
        </div>

         </li>
         
           <li>
        <div id="field-wrapper">
          <div class="control-group">
            <label for="field" id="field_label" class="control-label">Field #</label>
            <div class="controls">
               <input id='field' name='field' type='text' size='1' value=<?php print '"' .  $game['field_num'] . '"'; ?> class="input-small" placeholder="Field #">
            </div>
          </div>
        </div>

     
         </li>
         
       

         
         
         <li>
        <?php if (editCheck()) { ?>
        <div id="submit-wrapper">
          <div class="control-group">
            <label for="submit" id="submit_label" class="control-label">&nbsp;</label>
            <div class="controls">
               <input type='hidden' id='game_id' value=<?php print '"' . $game_id . '"' ?>>
               <input type='button' class='btn-no btn-flat red' id='eGame' name='eGame' value='Submit Edits' />
            </div>
          </div>
        </div>
        </li>
        <?php } ?>

      </div>
    </form>
  </div>
</div>


<?php
echo "<script type='text/javascript'>";
echo "$('.error').not(function(index){return $(this).hasClass('control-group');}).hide();";
echo "</script>";
