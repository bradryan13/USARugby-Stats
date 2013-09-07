<?php
include_once './include.php';

$comp_id = $request->get('id');
$comp = $db->getCompetition($comp_id);
?>

<div class="header"><div class="title container">
<h1><?php print $comp['name']; ?></h1></div>
<?php include_once './comp_info.php'; ?>
</div></div>

<div class="container">
<div id="maincontent">

<?php
echo "<div class='section-head'><h3><span>Teams</span></h3></div>";
echo "<div id='teams' class='row-fluid'><div class='span12'>";
// Get the teams in this comp
include_once './comp_teams.php';
echo "</div></div>";

if (editCheck(1)) {
    echo "<div id='addteamdiv'>";
    include_once './add_team.php';
    echo "</div>";
}

echo "<div class='clearfix'></div>";
echo "<h2>Games</h2>";
echo "<div id='games'>";
// Get the games in this comp
// Replace include_once with comp_games.twig
include_once './comp_games.php';
echo "</div></div></div>";

if (editCheck(1)) {
    echo "<div id='addgamediv'>";
    include_once './add_game.php';
    echo "</div>";
}

include_once './footer.php';
mysql_close();
