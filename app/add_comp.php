<?php
include_once './header.php';

$id = $request->get('id');
$title = 'Create a Competition';
$name = '';
$start_date = '';
$end_date = '';
$type = 0;
$max_event = '';
$max_match = '';
$hidden = 0;
$league_type = '';
$top_groups = array();
$roster_bypass = FALSE;

if (!empty($id)) {
    $competition = $db->getCompetition($id);
    $top_groups = $db->getCompetitionTopGroups($id);
    $title = "Edit: {$competition['name']}";
    $name = $competition['name'];
    $start_date = $competition['start_date'];
    $end_date = $competition['end_date'];
    $type = $competition['type'];
    $max_event = $competition['max_event'];
    $max_match = $competition['max_game'];
    $hidden = $competition['hidden'];
    $league_type = $competition['league_type'];
    $roster_bypass = $competition['bypass_checkin'];
}
?>

<h1><?php echo $title; ?></h1>

<form name='addcomp' id='addcomp' method='POST' action='add_comp_process.php'>
    <div class="alert error alert-error" id="form-validation-error">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <div class="error-message"></div>
    </div>

    <input id='id' name='id' type='hidden' value='<?php echo $id; ?>'>

    <label for="name" id="name_label" >Name</label>
    <input id='name' name='name' type='text' size='30' placeholder="Competition Name" class="required input-large" value='<?php echo $name;?>'>

    <label for="type" id="type_label">Type</label>
    <select name='type' id='type' data-placeholder="Type" class="required chzn-select">
        <option value=''></option>
        <option <?php if ($type == 1) echo 'selected'; ?> value='1'>15s</option>
        <option <?php if ($type == 2) echo 'selected'; ?> value='2'>7s</option>
    </select>

    <label for="league_type" id="league_label">League Type</label>
    <select name='league_type' id='league-type' data-placeholder="League Type" class="required chzn-select">
        <option value=''></option>
        <option <?php if ($league_type == 'League') echo 'selected'; ?> value='League'>League</option>
        <option <?php if ($league_type == 'Friendly') echo 'selected'; ?> value='Friendly'>Friendly</option>
        <option <?php if ($league_type == 'Playoffs') echo 'selected'; ?> value='Playoffs'>Playoffs</option>
    </select>

    <label for="top_groups" id="top_groups_label" class="control-label">League or Division</label>
    <select data-placeholder='League or Division' name='top_groups[]' id='top_groups' class="chzn-select" multiple="multiple">
        <option value=''></option>
        <?php
        // Give a list of every team to choose from.
        $teams = $db->getAllTeams();
        foreach ($teams as $uuid => $team) {
            if (in_array($team['id'], $top_groups)) {
                echo "<option selected value='{$team['id']}'>{$team['name']}</option>";
            }
            else {
                echo "<option value='{$team['id']}'>{$team['name']}</option>";
            }
        }
        ?>
    </select>

    <label id="date_time" class="control-label">Date And Time</label>
    <input id='start_date' name='start_date' type='text' size='10' class="date_select required input-small" placeholder="Start Date" value='<?php echo $start_date;?>'>
    <input id='end_date' name='end_date' type='text' size='10' class="date_select required input-small" placeholder="End Date" value='<?php echo $end_date;?>'>

    <label for="max_event" id="max_event_label">Maximum players on event roster</label>

    <select name='max_event' id='max_event' data-placeholder="Max Players on Event Roster" class="input-large chzn-select required">
        <option value=''></option>
            <?php
            for ($i = 10; $i <= 35; $i++) {
                if ($max_event == $i) {
                    echo '<option selected value="' . $i . '">' . $i . '</option>';
                }
                else {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
            }
        ?>
    <option <?php if ($max_event == 99) echo 'selected';?> value='99'>Unlimited</option>
    </select>

    <label for="max_event" id="max_event_label">Maximum players on match roster</label>
    <select name='max_match' id='max_match' data-placeholder="Max Players on Match Roster" class="input-large chzn-select required">
        <option value=''></option>
            <?php
            for ($i = 7; $i <= 25; $i++) {
                if ($max_match == $i) {
                    echo '<option selected value="' . $i . '">' . $i . '</option>';
                }
                else {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
            }
            ?>
        <option <?php if ($max_match == 99) echo 'selected';?> value='99'>Unlimited</option>
    </select>

    <label for="bypass_checkin" id="bypass_checkin_label">Bypass Check-In For Rosters</label>
    <input id="bypass_checkin" name="bypass_checkin" type="checkbox" <?php if ($roster_bypass) {echo 'checked="checked"';} ?>>

    <br/>
    <br/>
    <?php
        if (!empty($id)) {
            echo "<input type='submit' name='submit' class='button btn btn-primary' id='add_comp' value='Update Competition'>";
        }
        else {
            echo "<input type='submit' name='submit' class='button btn btn-primary' id='add_comp' value='Create Competition'>";
        }
    ?>
</form>

<?php
include_once './footer.php';
