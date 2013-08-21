<?php
include_once './include_mini.php';
if (editCheck(1)) {

    echo "<label for='login' id='login_label'>User Email</label>";
    echo "<input id='login-account' name='login' type='text' value='{$row['login']}'>";
    echo "<label class='error' for='login' id='login_error'>This field is required.</label><br/>";

    echo "<label for='team' id='team_label'>Team</label>";
    echo "<select name='team' id='team' class='chzn-select input-large'>";
    echo "<option value=''>No team (Admin / Ref only)</option>";
    $query2 = "SELECT id, name FROM `teams` WHERE hidden=0 ORDER BY name ASC";
    $result2 = mysql_query($query2);
    while ($row2=mysql_fetch_assoc($result2)) {
        echo "<option value='{$row2['id']}'>{$row2['name']}</option>";
    }
    echo "</select>";
    echo "<br/>";

    echo "<label for='access' id='access_label'>Access</label>";
    echo "<select name='access' id='access' data-placeholder='Access' class='input-large chzn-select'>";
    echo "<option value=''></option>";
    echo "<option value='1'>Administrator</option>";
    echo "<option value='2'>Referee</option>";
    echo "<option value='3'>Specific Team</option>";
    echo "<option value='4'>View Only</option>";
    echo "</select>";
    echo "<label class='error' for='access' id='access_error'>This field is required.</label>";
    echo "<br/>";

    echo "<input type='button' class='add btn btn-primary' id='addUser' name='addUser' value='Add User' />";
}
