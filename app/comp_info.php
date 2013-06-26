<?php
$query = "SELECT * FROM `comps` WHERE id = $comp_id";

$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $sdate = date('F j, Y', strtotime($row['start_date']));
    $edate = date('F j, Y', strtotime($row['end_date']));
    echo "First day of competition: $sdate<br/>\r";
    echo "Last day of competition: $edate<br/>\r";
    echo "Maximum players on event roster: {$row['max_event']}<br/>\r";
    echo "Maximum players on match roster: {$row['max_game']}<br/>\r";
}
