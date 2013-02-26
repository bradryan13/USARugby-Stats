<?php
include_once './include_mini.php';
if (editCheck(1)) {

    echo "<label for='login-aceess' id='login-access_label'>Access Code</label>";
    echo "<input id='login-access' name='login-access' type='text' value='{$row['login']}'>";
    echo "<label class='error' for='login-access' id='login_error_access'>This field is required.</label><br/>";

    echo "<label for='team-access' id='team_label'>Team</label>";
    echo "<select name='team-access' id='team-access' class='chzn-select input-large'>";
    echo "<option value=''>No team (Referee only)</option>";
    $query2 = "SELECT id, name FROM `teams` WHERE hidden=0 ORDER BY name ASC";
    $result2 = mysql_query($query2);
    while ($row2=mysql_fetch_assoc($result2)) {
        echo "<option value='{$row2['id']}'>{$row2['name']}</option>";
    }
    echo "</select>";
    echo "<label class='error' for='team-access' id='team_access_error'>This field is required.</label><br/>";
    echo "<br/>";

    echo "<label for='access-team' id='access-team_label'>Access</label>";
    echo "<select name='access-team' id='access-team' data-placeholder='Access' class='input-large chzn-select'>";
    echo "<option value=''></option>";
    echo "<option value='2'>Referee</option>";
    echo "<option value='3'>Specific Team</option>";
    echo "<option value='4'>View Only</option>";
    echo "</select>";
    echo "<label class='error' for='access' id='access_error'>This field is required.</label>";
    echo "<input id='proxy_user' name='proxy_user' type='hidden' value='{$_SESSION['user_id']}'>";
    echo "<br/>";

    echo "<input type='button' class='add btn btn-primary' id='addAccess' name='addAccess' value='Add Access' />";
}
