<?php
include_once './include_mini.php';
use Source\APSource;

$game_id = $_POST['game_id'];

//find if this game has game events
$query = "SELECT * FROM `game_events` WHERE game_id='$game_id'";
$result = mysql_query($query);
$numrows = mysql_numrows($result);

//if it does, don't delete and send back message to be alerted
if ($numrows) {
    echo "Game stats exist for this game.  Please delete game stats before you can delete this game.";
} else
    //no events? let's see if it was a 0-0 game with no subs that was confirmed
{
    $query = "SELECT ref_sign, 4_sign, home_sign, away_sign, uuid FROM `games` WHERE id='$game_id'";
    $result = mysql_query($query);
    while ($row=mysql_fetch_assoc($result)) {
        //if a signoff exists, send back message to be alerted
        if ($row['ref_sign'] || $row['4_sign'] || $row['home_sign'] || $row['away_sign']) {
            echo "Game has referee, #4, or coach confirmation.  Please remove confirmation before you can delete this game.";
        }
        else {
            // No events and no confirm means we can delete.
            $query = "DELETE FROM `games` WHERE id='$game_id'";
            $result = mysql_query($query);

            $query = "DELETE FROM `game_rosters` WHERE game_id='$game_id'";
            $result = mysql_query($query);

            // Delete the remote game from AP.
            if ($oauth_disabled) {
                $client = APSource::BasicAuthFactory($_SESSION['user'], $_SESSION['password']);
            }
            else {
                $client = APSource::SessionSourceFactory();
            }
            $client->deleteEvent($row['uuid']);
        }
    }
}
