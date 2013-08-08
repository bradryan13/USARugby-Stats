<?php
include_once './include_mini.php';

if (!isset($game_id) || !$game_id) {$game_id=$_GET['id'];}


echo "<form id='signform' name='signform' method='POST' action='' />";
echo "<ul>";

$query = "SELECT ref_sign,4_sign,away_sign,home_sign FROM `games` WHERE id = $game_id";
$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {

    if ($row['ref_sign']==1) {$checked='checked';} else {$checked='';}
    echo "<li>Ref Signature:";
    echo "<input type='checkbox' id='ref' name='ref' class='signbox' value='1' $checked/></li>";

    if ($row['4_sign']==1) {$checked='checked';} else {$checked='';}
    echo "<li>#4 Signature:";
    echo "<input type='checkbox' id='num4' name='num4' class='signbox' value='1' $checked/></li>";

    if ($row['home_sign']==1) {$checked='checked';} else {$checked='';}
    echo "<li>Home Coach Signature:";
    echo "<input type='checkbox' id='homec' name='homec' class='signbox' value='1' $checked/></li>";

    if ($row['away_sign']==1) {$checked='checked';} else {$checked='';}
    echo "<li class='last'>Away Coach Signature:";
    echo "<input type='checkbox' id='awayc' name='awayc' class='signbox' value='1' $checked/></li>";

}

echo "<input type='hidden' id='srefresh' name='srefresh' value='s.php?id=$game_id' />";
echo "<input type='hidden' id='game_id' name='game_id' value='$game_id' />";
echo "</table>";
echo "</form></div>";
