<?php
$query = "SELECT * FROM `comps` WHERE id = $comp_id";

$result = mysql_query($query);
while ($row=mysql_fetch_assoc($result)) {
    $sdate = date('F j, Y', strtotime($row['start_date']));
    $edate = date('F j, Y', strtotime($row['end_date']));
    ?>
    <div class="game-meta" id="game-id"><div class="container"><ul>
    <?php echo "<li class='meta'><span>$sdate - $edate</span>\r"; 
} ?> </ul></div>
