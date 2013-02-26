<?php
$query = "SELECT * FROM `event_rosters` WHERE team_id = $team_id";

$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {

    $query2 = "SELECT * FROM `comps` WHERE id = {$row['comp_id']}";
    $result2 = mysql_query($query2);
    while ($row2=mysql_fetch_assoc($result2)) {

        echo "<span class='normal'><a href='event_roster.php?id={$row['id']}'>{$row2['name']}</a></span><br/>";

    }

}
