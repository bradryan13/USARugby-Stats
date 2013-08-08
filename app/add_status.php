   <div class='add-controls status'>
   <div class='span4'>
    <form name='addstatus' id='addstatus' method='POST' action=''>
      <div class="alert error alert-error" id="form-validation-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="error-message"></div>
      </div>
      <div class="control">
                <select name='gamestatus' id='gamestatus' data-placeholder="Game Status" class='required input-medium chzn-select'>
                    <option value=""></option>
                    <?php
                    $status = game_status($game_id);
                    foreach (game_status_list() as $key => $value) {
                        if ($value == $status) {
                            echo "<option value='$key' selected='selected'>".$value."</option>";
                        } else {
                            echo "<option value='$key'>".$value."</option>";
                        }
                    }
                    ?>
                </select>
                 <input type='hidden' name='statusrefresh' id='statusrefresh' value='<?php echo $request->getScheme() . '://' . $request->getHost() . "/add_status.php"; ?>'>
                 <input type='hidden' name='status_game_id' id='status_game_id' value='<?php echo "$game_id"; ?>'>
                 <input type='submit' name='submit' class='btn-flat btn-no red' id='add_status' value='Add Status'>
    </div>
    </div>
    
                  </form>
                  

